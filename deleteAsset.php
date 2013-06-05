<form id="gbForm" action="deleteAsset.php" method="post">
	Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
	<input type="submit" value="Delete" />
</form>

<?php
require_once 'init.php';

if (!empty($_POST)){
    $assetId = $_POST['AssetIdTextBox'];
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $asset->Delete();
    
    echo 'Delete Successfully';
}
?>

