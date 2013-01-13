<?php

include_once('Constants.php');
include_once('Utility.php');

class Asset{
    private $context;
    
    public $__metadata;
    public $id;
    // 0 = initialized, 1 = deleted
    public $state;
    public $created;
    public $lastModified;
    public $alternateId;
    public $name;
    // 0 = none, 1 = storage encrypted, 2 = common encryption protected
    public $options;
    
    public function __construct($context, $id){
        $this->context = $context;
        if(isset($id)){
            $this->id = $id;
        }
    }
    
    public function Create(){
        if(strlen($this->name) == 0){
            $this->name = uniqid('asset_');
        }
        $createUri = sprintf('%sAssets/', $this->context->wamsEndpoint);
        $r = new HttpRequest($createUri, HttpRequest::METH_POST);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        $r->setContentType(RequestContentType::$JSON);
        $assetCreateRequestBodyFormat = '{ "Name":"%s", "State":"%d", "Options":"%d"}';
        $requestBody = sprintf($assetCreateRequestBodyFormat, 
                            $this->name, 
                            $this->state,
                            $this->options);
        $r->setBody($requestBody);
        try{
            $r->send();
            if ($r->getResponseCode() == 201) {
                $object = json_decode($r->getResponseBody());
                $this->__metadata = $object->d->__metadata;
                $this->id = $object->d->Id;
                $this->state = $object->d->State;
                $this->options = $object->d->Options;
                $this->alternateId = $object->d->AlternateId;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Delete(){
        $deleteUri = sprintf('%sAssets(\'%s\')', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($deleteUri, HttpRequest::METH_DELETE);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken())));
        try{
            $r->send();
            if($r->getResponseCode() != 204){
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Update(){
        $updateUri = sprintf('%sAssets(\'%s\')', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($updateUri, HttpRequest::METH_MERGE);
        $r->addHeaders(array(RequestHeaders::$ContentType => RequestContentType::$JSON,
                        RequestHeaders::$Accept => RequestContentType::$JSON,
                        RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken())));
        $r->setContentType(RequestContentType::$JSON);
        $assetUpdateRequestBodyFormat = '{ "Name":"%s", "AlternateId":"%s" }';
        $requestBody = sprintf($assetUpdateRequestBodyFormat, $this->name, $this->alternateId);
        $r->setBody($requestBody);
        try{
            $r->send();
            if($r->getResponseCode() != 204){
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Publish(){
        $publishUri = sprintf('%sAssets(\'%s\')/Publish', $this->context->wamsEndpoint, $this->id);
        $r = new HttpRequest($publishUri, HttpRequest::METH_POST);
        $r->addHeaders(array(RequestHeaders::$ContentType => RequestContentType::$JSON,
                        RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->context->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() != 204){
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function Get(){
        $getUri = sprintf('%sAssets(\'%s\')', $this->context->wamsEndpoint, $this->id);
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
                $this->__metadata = $object->d->__metadata;
                $this->id = $object->d->Id;
                $this->name = $object->d->Name;
                $this->state = $object->d->State;
                $this->options = $object->d->Options;
                $this->alternateId = $object->d->AlternateId;
                $this->created = Utility::DotNetJSONDateToTime($object->d->Created);
                $this->lastModified = Utility::DotNetJSONDateToTime($object->d->LastModified);
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
    }
    
    public function ListLocators(){
        $locators = array();
        $listUri = sprintf('%s/Assets(\'%s\')/Locators', $this->context->wamsEndpoint, $this->id);
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
                    $locator = $this->context->getLocatorReference();
                    $locator->id = $object['Id'];
                    $locator->expirationDateTime = Utility::DotNetJSONDateToTime($object['ExpirationDateTime']);
                    $locator->type = $object['Type'];
                    $locator->path = $object['Path'];
                    $locator->baseUri = $object['BaseUri'];
                    $locator->contentAccessComponent = $object['ContentAccessComponent'];
                    $locator->accessPolicyId = $object['AccessPolicyId'];
                    $locator->assetId = $object['AssetId'];
                    $locator->startTime = Utility::DotNetJSONDateToTime($object['StartTime']);
                    $locators[$i++] = $locator;
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $locators;
    }
    
    public function ListAccessPolicies(){
        $accessPolicies = array();
        $listUri = sprintf('%s/Assets(\'%s\')/AccessPolicies', $this->context->wamsEndpoint, $this->id);
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
                    $accessPolicy = $this->context->getAccessPolicyReference();
                    $accessPolicy->id = $object['Id'];
                    $accessPolicy->durationInMinutes = $object['DurationInMinutes'];
                    $accessPolicy->permissions = $object['Permissions'];
                    $accessPolicy->created = Utility::DotNetJSONDateToTime($object['Created']);
                    $accessPolicy->lastModified = Utility::DotNetJSONDateToTime($object['LastModified']);
                    $accessPolicy->name = $object['Name'];
                    $accessPolicies[$i++] = $accessPolicy;
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $accessPolicies;
    }
    
    public function UploadContent($mediaContext, $file_name, $tmp_name){
        $accessPolicy = $mediaContext->getAccessPolicyReference();
        $accessPolicy->name = 'uploadpolicy';
        $accessPolicy->durationInMinutes = 180;
        $accessPolicy->permissions = AccessPolicyPermission::$WRITE;
        $accessPolicy->Create();
        
        $locator = $mediaContext->getLocatorReference();
        $locator->assetId = $this->id;
        $locator->accessPolicyId = $accessPolicy->id;
        $locator->startTime = time() - (5 * 60);
        $locator->type = LocatorType::$SAS;
        $locator->Create();
        
        $uri = $locator->path;
        $newUri = Utility::GetFileUrl($uri, $file_name);
        $r = new HttpRequest($newUri, HttpRequest::METH_PUT);
        $r->addHeaders(array(RequestHeaders::$ContentType => RequestContentType::$BLOB,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$BlobXMsVersion,
                        RequestHeaders::$XMsDate => RequestHeadersValues::$BlobXMsDate,
                        RequestHeaders::$XMsBlobType => RequestHeadersValues::$BlobXMsBlobType));
        $r->setPutFile($tmp_name);
        try{
            $r->send();
            if($r->getResponseCode() != 201){
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        
        $locator->Delete();
        $accessPolicy->Delete();
    }
    
    public function ListAssetFiles(){
        $assetFiles = array();
        $listUri = sprintf('%sAssets(\'%s\')/Files', $this->context->wamsEndpoint, $this->id);
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
                    $assetFile = $this->context->getAssetFileReference();
                    $assetFile->id = $object['Id'];
                    $assetFile->name = $object['Name'];
                    $assetFile->contentFileSize = $object['ContentFileSize'];
                    $assetFile->parentAssetId = $object['ParentAssetId'];
                    $assetFile->encryptionVersion = $object['EncryptionVersion'];
                    $assetFile->encryptionScheme = $object['EncryptionScheme'];
                    $assetFile->isEncrypted = $object['IsEncrypted'];
                    $assetFile->encryptionKeyId = $object['EncryptionKeyId'];
                    $assetFile->initializationVector = $object['InitializationVector'];
                    $assetFile->isPrimary = $object['IsPrimary'];
                    $assetFile->mimeType = $object['MimeType'];
                    $assetFile->contentChecksum = $object['ContentChecksum'];
                    $assetFile->created = Utility::DotNetJSONDateToTime($object['Created']);
                    $assetFile->lastModified = Utility::DotNetJSONDateToTime($object['LastModified']);
                    $assetFiles[$i++] = $assetFile;
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $assetFiles;
    }
}

class AssetState{
    public static $INITIALIZED = 0;
    public static $DELETED = 1;
}

class AssetOptions{
    public static $NONE = 0;
    public static $STORAGE_ENCRYPTED = 1;
    public static $COMMON_ENCRYPTION_PROTECTED = 2;
}
?>