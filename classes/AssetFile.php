<?php
class AssetFile{
    private $context;
    
    public $id;
    public $name;
    public $contentFileSize;
    public $parentAssetId;
    public $encryptionVersion;
    public $encryptionScheme;
    public $isEncrypted;
    public $encryptionKeyId;
    public $initializationVector;
    public $isPrimary;
    public $lastModified;
    public $created;
    public $mimeType;
    public $contentChecksum;
    
    public function __construct($context, $id){
        $this->context = $context;
        $this->id = $id;
    }
    
    public function Get(){
        $getUri = sprintf('%sFiles(\'%s\')', $this->context->wamsEndpoint, $this->id);
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
                $this->contentFileSize = $object->d->ContentFileSize;
                $this->parentAssetId = $object->d->ParentAssetId;
                $this->encryptionVersion = $object->d->EncryptionVersion;
                $this->encryptionScheme = $object->d->EncryptionScheme;
                $this->isEncrypted = $object->d->IsEncrypted;
                $this->encryptionKeyId = $object->d->EncryptionKeyId;
                $this->initializationVector = $object->d->InitializationVector;
                $this->isPrimary = $object->d->IsPrimary;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
                $this->mimeType = $object->d->MimeType;
                $this->contentChecksum = $object->d->ContentChecksum;
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
?>