<?php
class MediaProcessor{
    public $id;
    public $name;
    public $description;
    public $sku;
    public $vendor;
    public $version;
    
    public function __construct($id){
        $this->id = $id;
    }
}
?>