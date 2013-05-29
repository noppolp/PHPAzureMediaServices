<?php
include_once('Task.php');

class Job{
    private $context;
    
    public $id;
    public $name;
    public $created;
    public $lastModified;
    public $endTime;
    public $priority;
    public $runningDuration;
    public $startTime;
    public $state;
    public $templateId;
    public $inputMediaAssets;
    public $outputMediaAssets;
    public $tasks;
    
    public function __construct($context, $name, $id){
        $this->context = $context;
        $this->id = $id;
        $this->name = $name;
        $this->tasks = array();
        $this->inputMediaAssets = array();
        $this->outputMediaAssets = array();
    }
    
    public function AddNewTask($taskName, $mediaProcessor, $configuration, $taskOptions = 0){
        $task = new Task($this, $taskName, $mediaProcessor->id, $configuration, $taskOptions);
        $this->tasks[count($this->tasks)] = $task;
        return $task;
    }

    public function Submit(){
        $submitUri = sprintf('%sJobs', $this->context->wamsEndpoint);
        $r = new HttpRequest($submitUri, HttpRequest::METH_POST);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON,
                        RequestHeaders::$ContentType => RequestContentType::$JSON));
        $jobSubmitFormat = '{"Name" : "%s", "InputMediaAssets" : %s, "Tasks" : %s}';
        $requestBody = sprintf($jobSubmitFormat, 
                            $this->name,
                            $this->getInputMediaAssetsJSON(),
                            $this->getTasksJSON());
        $r->setBody($requestBody);
        try{
            $r->send();
            if ($r->getResponseCode() == 201) {
                $object = json_decode($r->getResponseBody());
                $this->id = $object->d->Id;
                $this->name = $object->d->Name;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
                $this->endTime = Utility::DotNetJSONDateToTime($object->d->EndTime);
                $this->priority = $object->d->Priority;
                $this->runningDuration = $object->d->RunningDuration;
                $this->startTime = Utility::DotNetJSONDateToTime($object->d->StartTime);
                $this->state = $object->d->State;
                $this->templateId = $object->d->TemplateId;
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    $this->Submit();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Get(){
        $getUri = sprintf('%sJobs(\'%s\')', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($getUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if ($r->getResponseCode() == 200) {
                $object = json_decode($r->getResponseBody());
                $this->id = $object->d->Id;
                $this->name = $object->d->Name;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
                $this->endTime = Utility::DotNetJSONDateToTime($object->d->EndTime);
                $this->priority = $object->d->Priority;
                $this->runningDuration = $object->d->RunningDuration;
                $this->startTime = Utility::DotNetJSONDateToTime($object->d->StartTime);
                $this->state = $object->d->State;
                $this->templateId = $object->d->TemplateId;
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    $this->Get();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function GetState(){
        //TODO: Get Job's State
    }
    
    public function Cancel(){
        //TODO: Cancel Job
    }
    
    public function ListTasks(){
        $tasks = array();
        $listUri = sprintf('%sJobs(\'%s\')/Tasks', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $task = new Task($this, $object['Name']);
                    $task->id = $object['Id'];
                    $task->name = $object['Name'];
                    $task->progress = $object['Progress'];
                    //TODO: Add More Task Properties
                    $tasks[$i++] = $task;
                }
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    return $this->ListTasks();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $tasks;
    }
    
    public function ListOutputAssets(){
        $assets = array();
        $listUri = sprintf('%sJobs(\'%s\')/OutputMediaAssets', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $asset = $this->context->getAssetReference();
                    $asset->id = $object['Id'];
                    //TODO
                    $assets[$i++] = $asset;
                }
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    return $this->ListOutputAssets();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $assets;
    }
    
    private function getInputMediaAssetsJSON(){
        $json = null;
        if(count($this->inputMediaAssets) > 0){
            $json = '[';
            $i = 0;
            foreach($this->inputMediaAssets as $inputMediaAsset){
                $format ='{"__metadata" : {"uri" : "%s"}}';
                $json = $json . sprintf($format, $inputMediaAsset->__metadata->uri);
                if($i != count($this->inputMediaAssets) - 1){
                    $json = $json . ', ';
                }
                $i++;
            }
            $json = $json . ']';
        }
        return $json;
    }
    
    private function getTasksJSON(){
        $json = null;
        if(count($this->tasks) > 0){
            $json = '[';
            $i = 0;
            foreach($this->tasks as $task){
                $format ='{"Name" : "%s", "Options" : "%d", "Configuration" : "%s", "MediaProcessorId" : "%s", "TaskBody" : "%s"}';
                $json = $json . sprintf($format, $task->name, $task->options, $task->configuration, $task->mediaProcessorId, $task->getTaskBody());
                if($i != count($this->tasks) - 1){
                    $json = $json . ', ';
                }
                $i++;
            }
            $json = $json . ']';
        }
        return $json;
    }
    
}

class JobState{
    public static $QUEUED = 0;
    public static $SCHEDULED = 1;
    public static $PROCESSING = 2;
    public static $FINISHED = 3;
    public static $ERROR = 4;
    public static $CANCELED = 5;
    public static $CANCELING = 6;
    
    public static function GetJobStateString($state){
        if($state == 0){
            return 'Queued';            
        }else if($state == 1){
            return 'Scheduled';            
        }else if($state == 2){
            return 'Processing';            
        }else if($state == 3){
            return 'Finished';            
        }else if($state == 4){
            return 'Error';            
        }else if($state == 5){
            return 'Canceled';            
        }else if($state == 6){
            return 'Canceling';            
        }
    }
}
?>