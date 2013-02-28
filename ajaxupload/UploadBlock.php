<?php
require_once '..\config.php';
require_once '..\classes\vendor\autoload.php';
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\BlockList;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
include_once('FileUploadModel.php');
require_once '..\classes\MediaServiceContext.php';
session_start();
if ($_SESSION['fum'])
{
    $storageAccountName = AzureInfo::$storageAccountName;
    $storageAccountKey = AzureInfo::$storageAccountKey;
    $connectionString = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', $storageAccountName, $storageAccountKey);
    $blobRestProxyClient = ServicesBuilder::getInstance()->createBlobService($connectionString);
    $fum = $_SESSION['fum'];
    $id = $_GET['id'];
    $blockId = str_pad($id, 5, "0", STR_PAD_LEFT);
    $fileSlice = fopen($_FILES['Slice']['tmp_name'], 'r');
    clearstatcache(false, $_FILES['Slice']['tmp_name']);
    $fileData = fread($fileSlice, filesize($_FILES['Slice']['tmp_name']));
    try
    {
    	$blobRestProxyClient->createBlobBlock($fum->container, $fum->fileName, $blockId, $fileData);
    }
    catch (ServiceException $exception)
    {
        $_SESSION['fum'] = null;
        $fum->isUploadCompleted = true;
        $fum->uploadStatusMessage = 'File could not be uploaded. ' . $exception->getErrorReason();
        echo json_encode(new UploadBlockResponse(true, false, $fum->uploadStatusMessage));
        exit();
    }
    
    $fum->blockList->addLatestEntry($blockId);
    if ($id == $fum->blockCount)
    {
        $fum->isUploadCompleted = true;
        $errorInOp = false;
        try
        {
        $accountName = AzureInfo::$accountName;
        $accountKey = AzureInfo::$accountKey;
        $mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
        $mediaContext->checkForRedirection();
        $blobRestProxyClient->commitBlobBlocks($fum->container, $fum->fileName, $fum->blockList);
        $asset = $mediaContext->getAssetReference($fum->assetId);
        $asset->Get();
        $asset->Publish();
        $fum->uploadStatusMessage = 'File has been uploaded successfully';
        $_SESSION['fum'] = null;
        }
        catch (Exception $exception)
        {
            $fum->uploadStatusMessage = 'File could not be uploaded.';
            $errorInOp = true;
            $_SESSION['fum'] = null;
        }
        echo json_encode(new UploadBlockResponse($errorInOp, $fum->isUploadCompleted, $fum->uploadStatusMessage));
        exit();
    }
    $_SESSION['fum'] = $fum;
}else
{
    echo json_encode(new UploadBlockResponse(true, false, 'File could not be uploaded.'));
    exit();
}
echo json_encode(new UploadBlockResponse(false, false, ''));

?>