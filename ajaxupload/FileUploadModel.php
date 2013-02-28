<?php
class FileUploadModel{
    public $blockCount;
    public $fileName;
    public $fileSize;
    public $startTime;
    public $isUploadCompleted;
    public $uploadStatusMessage;
    public $container;
    public $blockList;
    public $assetId;
}

class UploadBlockResponse
{
    public function __construct($error, $isLastBlock, $message){
        $this->error = $error;
        $this->isLastBlock = $isLastBlock;
        $this->message = $message;
    }
    
	public $error;
    public $isLastBlock;
    public $message;
}

?>