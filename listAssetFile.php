<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
    <input type="submit" value="Get Asset Files" />
</form>

<?php
require_once '/classes/MediaServiceContext.php';
require_once 'init.php';

if (!empty($_POST)){
    $assetId = $_POST['AssetIdTextBox'];

    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $assetFiles = $asset->ListAssetFiles();

    foreach($assetFiles as $file){
        echo '<br/><b>File Name:</b> ' . $file->name;
    }
}
?>