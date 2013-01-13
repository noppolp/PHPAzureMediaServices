<?php
class TaskHistoricalEvent{
    public $code;
    public $message;
    public $timeStamp;
    
    public function __construct($code, $message, $timeStamp){
        $this->code = $code;
        $this->message = $message;
        $this->timeStamp = $timeStamp;
    }
}
?>