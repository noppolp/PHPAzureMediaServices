<?php
include_once('Constants.php');
include_once('Utility.php');

class Locator{
    private $context;
    
    public $id;
    public $expirationDateTime;
    //None = 0, SAS = 1, OnDemandOrigin = 2
    public $type;
    public $path;
    public $baseUri;
    public $contentAccessComponent;
    public $accessPolicyId;
    public $assetId;
    public $startTime;
    
    public function __construct($context, $id){
        $this->context = $context;
        $this->id = $id;
    }

    public function Create(){
        $createUri = sprintf('%sLocators/', $this->context->wamsEndpoint);
        $r = new HttpRequest($createUri, HttpRequest::METH_POST);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON,
                        RequestHeaders::$ContentType => RequestContentType::$JSON));
        $locatorCreateFormat = '{ "AccessPolicyId":"%s", "AssetId":"%s", "StartTime":"%s", "Type":"%d"}';
        $requestBody = sprintf($locatorCreateFormat, 
                            $this->accessPolicyId, 
                            $this->assetId,
                            Utility::TimeToEnUSFormat($this->startTime),
                            $this->type);
        $r->setBody($requestBody);
        try{
            $r->send();
            if ($r->getResponseCode() == 201) {
                $object = json_decode($r->getResponseBody());
                $this->id = $object->d->Id;
                $this->expirationDateTime = Utility::DotNetJSONDateToTime($object->d->ExpirationDateTime);
                $this->type = $object->d->Type;
                $this->path = $object->d->Path;
                $this->baseUri = $object->d->BaseUri;
                $this->contentAccessComponent = $object->d->ContentAccessComponent;
                $this->accessPolicyId = $object->d->AccessPolicyId;
                $this->assetId = $object->d->AssetId;
                $this->startTime = Utility::DotNetJSONDateToTime($object->d->StartTime);
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
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
        $deleteUri = sprintf('%sLocators(\'%s\')', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($deleteUri, HttpRequest::METH_DELETE);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken())));
        try{
            $r->send();
            if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->context->wamsEndpoint){
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
        $getUri = sprintf('%sLocators(\'%s\')', $this->context->wamsEndpoint, $this->id);
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
                $this->expirationDateTime = Utility::DotNetJSONDateToTime($object->d->ExpirationDateTime);
                $this->type = $object->d->Type;
                $this->path = $object->d->Path;
                $this->baseUri = $object->d->BaseUri;
                $this->contentAccessComponent = $object->d->ContentAccessComponent;
                $this->accessPolicyId = $object->d->AccessPolicyId;
                $this->assetId = $object->d->AssetId;
                $this->startTime = Utility::DotNetJSONDateToTime($object->d->StartTime);
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
}

class LocatorType{
    //None = 0, SAS = 1, OnDemandOrigin = 2
    public static $NONE = 0;
    public static $SAS = 1;
    public static $ON_DEMAND_ORIGIN = 2;
}
?>