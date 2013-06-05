<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
    <input type="file" name="blobfile" id="blobfile" size="16" value="" />
    <input type="submit" value="Upload" />
</form>
<?php
require_once '/classes/MediaServiceContext.php';
require_once 'init.php';

date_default_timezone_set("GMT");

if (!empty($_POST)){
    if(is_uploaded_file($_FILES['blobfile']['tmp_name'])){
        $assetId = $_POST['AssetIdTextBox'];
        
        $accessPolicy = $mediaContext->getAccessPolicyReference();
        $accessPolicy->name = 'Upload Content AP';
        $accessPolicy->durationInMinutes = 120;
        $accessPolicy->permissions = AccessPolicyPermission::$WRITE;
        $accessPolicy->Create();
        
        $locator = $mediaContext->getLocatorReference();
        $locator->assetId = $assetId;
        $locator->accessPolicyId = $accessPolicy->id;
        $locator->startTime = time() - (5 * 60);
        $locator->type = LocatorType::$SAS;
        $locator->Create();
        $asset = $mediaContext->getAssetReference($assetId);
        $asset->Get();
        $asset->UploadContent($_FILES['blobfile']['name'], $_FILES['blobfile']['tmp_name']);
        $asset->Publish();
        
        echo '<br/> Upload Completed !';
    }
    
}

?>