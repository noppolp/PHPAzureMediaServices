<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
    <input type="submit" value="Get Url" />
</form>
<?php
require_once '/classes/MediaServiceContext.php';
require_once 'init.php';

//$accountName = 'noppolmedia';
//$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
//$storageAccountName = 'noppolpstorage';
//$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
//$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
//$mediaContext->checkForRedirection();

if (!empty($_POST)){
    $assetId = $_POST['AssetIdTextBox'];
    
    $accessPolicy = $mediaContext->getAccessPolicyReference();
    $accessPolicy->name = 'Stream AC';
    $accessPolicy->durationInMinutes = 30000;
    $accessPolicy->permissions = AccessPolicyPermission::$READ;
    $accessPolicy->Create();
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $assetFiles = $asset->ListAssetFiles();
    $locator = $mediaContext->getLocatorReference();
    $locator->assetId = $asset->id;
    $locator->accessPolicyId = $accessPolicy->id;
    $locator->startTime = time() - (5 * 60);
    $locator->type = LocatorType::$SAS;
    $locator->Create();
    $mp4File = null;
    foreach($assetFiles as $file){
        if (strpos($file->name,'.mp4') !== false) {
            $mp4File = $file;
        }
    }
    echo '<br/><b>File Name:</b> ' . $mp4File->name;
    echo '<br/><b>Locator Path:</b> ' . $locator->path;
    echo '<br/><b>Output Path:</b> ' . Utility::GetFileUrl($locator->path, $mp4File->name);
}
?>