<?php
class AcsToken{
    public $token_type;
    public $access_token;
    public $expires_in;
    public $scope;
    
    public function __construct($token_type, $access_token, $expires_in, $scope){
        $this->token_type = $token_type;
        $this->access_token = $access_token;
        $this->expires_in = $expires_in;
        $this->scope = $scope;
    }
    
    public static function createFromJson($jsonString){
        $object = json_decode($jsonString);
        return new self($object->token_type, $object->access_token, $object->expires_in, $object->scope);
    }
}
?>