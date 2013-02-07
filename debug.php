<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
	Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/>
	<input type="file" name="blobfile" id="blobfile" size="16" value="" />
	<input type="submit" value="Upload" />
</form>

<?php
//require_once 'init.php';

//$assetId = 'nb:cid:UUID:0d19d391-2fb3-4cc0-9b54-311207dd7eb3';
//$asset = $mediaContext->getAssetReference($assetId);
//$asset->Get();
//echo $asset->GetStorageContainerName();
if (!empty($_POST)){
	echo 'POST ';
	if(is_uploaded_file($_FILES['blobfile']['tmp_name'])){
		echo 'FILE ';
		$filePointer = fopen($_FILES['blobfile']['tmp_name'], 'rb');
		$fileData = fread($filePointer, 1024);
		$blobContainer = 'asset-24a54809-cd31-432a-b020-4af8e11a3aad';
		$blobRestProxy->createBlobBlock($blobContainer, '6.wmv', 1, $fileData);
	}
}else
{
	echo 'NO POST';
}

//echo $asset->GetReadAccessUriByFileName('6.wmv');
//$blobContainer = 'asset-24a54809-cd31-432a-b020-4af8e11a3aad';
//$blobList = $blobRestProxy->listBlobBlocks($blobContainer, '6.wmv');
//$ucBlocks = $blobList->getUncommittedBlocks();
//echo '<br/>';
//foreach ($ucBlocks as $block)
//{
//	echo $block;
//}
$i = 5000;
$blockId = str_pad($i, 5, "0", STR_PAD_LEFT);
echo $blockId;
?>
