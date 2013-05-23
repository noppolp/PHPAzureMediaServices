<?php
class AccessPolicy{
    private $context;
    
    public $id;
    public $name;
    public $durationInMinutes;
    //None = 0, Read = 1, Write = 2, Delete = 4, List = 8
    public $permissions;
    public $created;
    public $lastModified;
    
    public function __construct($context, $id){
        $this->context = $context;
        $this->id = $id;
    }

    public function Create(){
        if(strlen($this->name) == 0){
            $this->name = uniqid('asset_');
        }
        $createUri = sprintf('%sAccessPolicies/', $this->context->wamsEndpoint);
        $r = new HttpRequest($createUri, HttpRequest::METH_POST);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON,
                        RequestHeaders::$ContentType => RequestContentType::$JSON));
        $accessPolicyCreateFormat = '{ "Name":"%s", "DurationInMinutes":"%d", "Permissions":"%d"}';
        $requestBody = sprintf($accessPolicyCreateFormat, 
                            $this->name, 
                            $this->durationInMinutes,
                            $this->permissions);
        $r->setBody($requestBody);
        try{
            $r->send();
            if ($r->getResponseCode() == 201) {
                $object = json_decode($r->getResponseBody());
                $this->id = $object->d->Id;
                $this->durationInMinutes = $object->d->DurationInMinutes;
                $this->permissions = $object->d->Permissions;
                $this->name = $object->d->Name;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    $this->Create();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Delete(){
        $deleteUri = sprintf('%sAccessPolicies(\'%s\')', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($deleteUri, HttpRequest::METH_DELETE);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken())));
        try{
            $r->send();
            if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->context->wamsEndpoint = $newLocation;
                    $this->Delete();
                }
            }else if($r->getResponseCode() != 204){
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
            
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Get(){
        $getUri = sprintf('%sAccessPolicies(\'%s\')', $this->context->wamsEndpoint, $this->id);
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
                $this->durationInMinutes = $object->d->DurationInMinutes;
                $this->permissions = $object->d->Permissions;
                $this->name = $object->d->Name;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
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
}

class AccessPolicyPermission{
    public static $NONE = 0;
    public static $READ = 1;
    public static $WRITE = 2;
    public static $DELETE = 3;
    public static $LIST = 8;
}
?>