<?php
include_once('Job.php');

class Task{
    private $job;
    private $inputAssetBody;
    private $outputAssetBody;
    
    public $id;
    public $configuration;
    public $endTime;
    public $errorDetails;
    public $historicalEvents;
    public $mediaProcessorId;
    public $name;
    public $perfMessage;
    public $priorty;
    public $progress;
    public $runningDuration;
    public $startTime;
    public $state;
    public $taskBody;
    public $options;
    public $encryptionKeyId;
    public $encryptionScheme;
    public $encrptionVersion;
    public $initializationVector;
    public $outputMediaAssets;
    public $inputMediaAssets;
    
    public function __construct($job, $name, $mediaProcessorId = null, $configuration = null, $options = null){
        $this->job = $job;
        $this->name = $name;
        $this->mediaProcessorId = $mediaProcessorId;
        $this->configuration = $configuration;
        $this->options = $options;
        $this->inputMediaAssets = array();
        $this->outputMediaAssets = array();
    }
    
    public function AddInputMediaAsset($asset){
        $this->inputMediaAssets[count($this->inputMediaAssets)] = $asset;
        if(count($this->job->inputMediaAssets) == 0){
            $this->job->inputMediaAssets[count($this->inputMediaAssets)] = $asset;
        }
        $inputAssetBodyFormat = '<inputAsset>%s</inputAsset>';
        $i = 0;
        foreach($this->job->inputMediaAssets as $inputAsset){
            if($asset == $inputAsset){
                $this->inputAssetBody = sprintf($inputAssetBodyFormat, 'JobInputAsset('. $i .')');
                return;
            }
            $i++;
        }
        $i = 0;
        foreach($this->job->outputMediaAssets as $outputAsset){
            if($asset == $outputAsset){
                $this->inputAssetBody = sprintf($inputAssetBodyFormat, 'JobOutputAsset('. $i .')');
                return;
            }
            $i++;
        }
        $this->job->inputMediaAssets[count($this->inputMediaAssets)] = $asset;
    }
    
    public function AddNewOutputMediaAsset($name, $options = 0){
        $newAsset = new Asset(null, null);
        $newAsset->name = $name;
        $newAsset->options = $options;
        $this->job->outputMediaAssets[count($this->job->outputMediaAssets)] = $newAsset;   
        $this->outputMediaAssets[count($this->outputMediaAssets)] = $newAsset;
        $outputAssetBodyFormat = '<outputAsset assetName=\\"%s\\" assetCreationOptions=\\"%d\\">%s</outputAsset>';
        $this->outputAssetBody = sprintf($outputAssetBodyFormat,$name, $options, sprintf('JobOutputAsset(%d)', count($this->job->outputMediaAssets) - 1));
    }
    
    public function getTaskBody(){
        $taskBodyFormat = '<?xml version=\\"1.0\\" encoding=\\"utf-8\\"?><taskBody>%s%s</taskBody>';
        $this->taskBody = sprintf($taskBodyFormat, $this->inputAssetBody, $this->outputAssetBody);
        return $this->taskBody;
    }
    
    public function Get(){
        //TODO
    }
    
    public function GetProgress(){
        //TODO
    }
}

class TaskState{
    public static $NONE = 0;
    public static $ACTIVE = 1;
    public static $RUNNING = 2;
    public static $COMPLETED = 3;
    
    public function getStateName($state){
        if($state == 0){
            return 'None';
        }else if($state == 1){
            return 'Active';
        }else if($state == 2){
            return 'Running';
        }else if($state == 3){
            return 'Completed';
        }
    }
}

class TaskOptions{
    public static $NONE = 0;
    public static $PROTECTED_CONFIGURATION = 1;
}
?>