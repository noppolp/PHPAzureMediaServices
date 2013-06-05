<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/><br/>
    File Name: <input type="text" id="FileNameTextBox" name="FileNameTextBox" class="field" size="50"/>
    <input type="submit" value="Get Url" />
</form>
<?php
require_once '/classes/MediaServiceContext.php';
require_once 'init.php';

date_default_timezone_set("GMT");

if (!empty($_POST)){
    $assetId = $_POST['AssetIdTextBox'];
    $fileName = $_POST['FileNameTextBox'];
    
    $accessPolicy = $mediaContext->getAccessPolicyReference();
    $accessPolicy->name = 'Stream AC';
    $accessPolicy->durationInMinutes = 30000;
    $accessPolicy->permissions = AccessPolicyPermission::$READ;
    $accessPolicy->Create();
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $locator = $mediaContext->getLocatorReference();
    $locator->assetId = $asset->id;
    $locator->accessPolicyId = $accessPolicy->id;
    $locator->startTime = time() - (5 * 60);
    $locator->type = LocatorType::$SAS;
    $locator->Create();
    
    echo '<br/><b>File Name:</b> ' . $fileName;
    echo '<br/><b>Locator Path:</b> ' . $locator->path;
    echo '<br/><b>Output Path:</b> ' . Utility::GetFileUrl($locator->path, $fileName);
}
?>