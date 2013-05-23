<?php
include_once('AcsToken.php');
include_once('Asset.php');
include_once('AccessPolicy.php');
include_once('Locator.php');
include_once('AssetFile.php');
include_once('MediaProcesser.php');
include_once('Job.php');

class MediaServiceContext{
    
    private $acsEndpoint = 'https://wamsprodglobal001acs.accesscontrol.windows.net/v2/OAuth2-13';
    private $accountName;
    private $accountKey;
    public $accessToken;
    private $accessTokenExpiry;
    public $wamsEndpoint = 'https://media.windows.net/API/';
    private $storageConnStr;
    
    public function __construct($accountName, $accountKey, $storageAccountName, $storageAccountKey){
        $this->accountName = $accountName;
        $this->accountKey = $accountKey;
        $this->storageConnStr = 'DefaultEndpointsProtocol=https;AccountName='. $storageAccountName .';AccountKey=' . $storageAccountKey;
    }
    
    public function getAccessToken(){
        if(strlen($this->accessToken) == 0 || $this->accessTokenExpiry < time()){
            $this->fetchNewAccessToken();
        }
        return $this->accessToken;
    }
    
    public function fetchNewAccessToken(){
        $r = new HttpRequest($this->acsEndpoint, HttpRequest::METH_POST);
        $r->addHeaders(array('Content-Type' => 'application/x-www-form-urlencoded',
        'Expect' => '100-continue', 
        'Connection' => 'Keep-Alive'));
        
        $getAccessTokenBodyFormat = 'grant_type=client_credentials&client_id=%s&client_secret=%s&scope=urn%%3aWindowsAzureMediaServices';
        $requestBody = sprintf($getAccessTokenBodyFormat, $this->accountName, urlencode($this->accountKey));
        $r->setBody($requestBody);
        
        try {
            $r->send();
            if ($r->getResponseCode() == 200) {
                $acsToken = AcsToken::createFromJson($r->getResponseBody());
                $this->accessToken = $acsToken->access_token;
                $this->accessTokenExpiry = time() + $acsToken->expires_in;
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        } catch (HttpException $ex) {
            echo $ex;
        }
    }
    
    public function checkForRedirection(){
        $r = new HttpRequest($this->wamsEndpoint, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->getAccessToken()), 
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion));
        try {
            $r->send();
            if($r->getResponseCode() == 301){
               $newLocation = $r->getResponseHeader('Location');
               if($newLocation != $this->wamsEndpoint){
                    $this->wamsEndpoint = $newLocation;
                    $this->accessToken = null;
               }
            }
        } catch (HttpException $ex){
            echo $ex;
        }
    }
    
    public function getAssetReference($assetId = null){
            return new Asset($this, $assetId);   
    }
    
    public function ListAssets(){
        $assets = array();
        $listUri = sprintf('%sAssets', $this->wamsEndpoint);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $asset = $this->getAssetReference();
                    $asset->__metadata = $object['__metadata'];
                    $asset->id = $object['Id'];
                    $asset->state = $object['State'];
                    $asset->options = $object['Options'];
                    $asset->alternateId = $object['AlternateId'];
                    $asset->created = Utility::DotNetJSONDateToTime($object['Created']);
                    $asset->lastModified = Utility::DotNetJSONDateToTime($object['LastModified']);
                    $asset->name = $object['Name'];
                    $assets[$i++] = $asset;  
                }
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->wamsEndpoint = $newLocation;
                    $this->accessToken = null;
                    return $this->ListAssets();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $assets;
    }
    
    public function getAccessPolicyReference($accessPolicyId = null){
        return new AccessPolicy($this, $accessPolicyId); 
    }
    
    public function ListAccessPolicies(){
        $accessPolicies = array();
        $listUri = sprintf('%sAccessPolicies', $this->wamsEndpoint);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $accessPolicy = $this->getAccessPolicyReference();
                    $accessPolicy->id = $object['Id'];
                    $accessPolicy->durationInMinutes = $object['DurationInMinutes'];
                    $accessPolicy->permissions = $object['Permissions'];
                    $accessPolicy->created = Utility::DotNetJSONDateToTime($object['Created']);
                    $accessPolicy->lastModified = Utility::DotNetJSONDateToTime($object['LastModified']);
                    $accessPolicy->name = $object['Name'];
                    $accessPolicies[$i++] = $accessPolicy;
                }
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->wamsEndpoint = $newLocation;
                    $this->accessToken = null;
                    return $this->ListAccessPolicies();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $accessPolicies;
    }
    
    public function getLocatorReference($id = null){
        return new Locator($this, $id);
    }
    
    public function ListLocators(){
        $locators = array();
        $listUri = sprintf('%sLocators', $this->wamsEndpoint);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $locator = $this->getLocatorReference();
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
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->wamsEndpoint = $newLocation;
                    $this->accessToken = null;
                    return $this->ListLocators();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $locators;
    }
    
    public function getAssetFileReference($id = null){
        return new AssetFile($this, $id);
    }
    
    public function ListMediaProcessors(){
        $mediaProcessors = array();
        $listUri = sprintf('%sMediaProcessors', $this->wamsEndpoint);
        $r = new HttpRequest($listUri, HttpRequest::METH_GET);
        $r->addHeaders(array(RequestHeaders::$DataServiceVersion => RequestHeadersValues::$DataServiceVersion,
                        RequestHeaders::$MaxDataServiceVersion => RequestHeadersValues::$MaxDataServiceVersion,
                        RequestHeaders::$XMsVersion => RequestHeadersValues::$XMsVersion,
                        RequestHeaders::$Authorization => sprintf(RequestHeadersValues::$Authorization, $this->getAccessToken()),
                        RequestHeaders::$Accept => RequestContentType::$JSON));
        try{
            $r->send();
            if($r->getResponseCode() == 200){
                $objects = json_decode($r->getResponseBody(), true);
                $i = 0;
                $results = $objects['d']['results'];
                foreach($results as $object){
                    $mediaProcessor = new MediaProcessor($object['Id']);
                    $mediaProcessor->name = $object['Name'];
                    $mediaProcessor->description = $object['Description'];
                    $mediaProcessor->sku = $object['Sku'];
                    $mediaProcessor->vendor = $object['Vendor'];
                    $mediaProcessor->version = $object['Version'];
                    $mediaProcessors[$i++] = $mediaProcessor;
                }
            }else if($r->getResponseCode() == 301){
                $newLocation = $r->getResponseHeader('Location');
                if($newLocation != $this->wamsEndpoint){
                    $this->wamsEndpoint = $newLocation;
                    $this->accessToken = null;
                    return $this->ListMediaProcessors();
                }
            }else{
                echo $r->getResponseCode() . ' ' . $r->getResponseBody();
            }
        }catch(HttpException $ex){
            echo $ex;
        }
        return $mediaProcessors;
    }
    
    public function getMediaProcessor($name){
        $mediaProcessors = $this->ListMediaProcessors();
        $result = null;
        foreach($mediaProcessors as $mediaProcessor){
            if($mediaProcessor->name == $name){
                $result = $mediaProcessor;
            }
        }
        if($result == null){
            echo 'Unknown Processor';            
        }
        return $result;
    }
    
    public function getJobReferenceByName($name){
        return new Job($this, $name, null);
    }
    
    public function getJobReference($id){
        return new Job($this, null, $id);
    }
}
?>