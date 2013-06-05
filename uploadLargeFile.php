<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
	Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
	<input type="file" name="blobfile" id="blobfile" size="16" value="" />
	<input type="submit" value="Upload" />
</form>

<?php
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\BlockList;
use WindowsAzure\Blob\Models\CreateBlobOptions;

date_default_timezone_set("GMT");

if (!empty($_POST)){
	if(is_uploaded_file($_FILES['blobfile']['tmp_name'])){
        require_once 'init.php';
        require_once 'initStorage.php';
        set_time_limit(0);
        $assetId = $_POST['AssetIdTextBox'];
        $asset = $mediaContext->getAssetReference($assetId);
        $asset->Get();
        $blobContainer = $asset->GetStorageContainerName();
        $filePointer = fopen($_FILES['blobfile']['tmp_name'], 'rb');
        $fileName = $_FILES['blobfile']['name'];
        $i = 1;
        $blockList = new BlockList();
        while (!feof($filePointer)){
            $fileData = fread($filePointer, 1024 * 1024 * 4); //4MB Maximum per piece (50,000 Maximum pieces per file or 200GB Maximum per file)
            $blockId = str_pad($i, 5, "0", STR_PAD_LEFT);
            $blobRestProxy->createBlobBlock($blobContainer, $fileName, $blockId, $fileData);
            $blockList->addLatestEntry($blockId);
            $i++;
        }
        $blobRestProxy->commitBlobBlocks($blobContainer, $fileName, $blockList);
        $asset->Publish();
        echo 'Upload Completed';
	}
}
?>
