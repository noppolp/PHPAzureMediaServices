<?php
require_once '..\config.php';
require_once '..\classes\vendor\autoload.php';
use WindowsAzure\Blob\Models\Block;
use WindowsAzure\Blob\Models\BlockList;
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
include_once('FileUploadModel.php');

if (!empty($_POST)){
    $blocksCount = $_POST['blocksCount'];
    $fileName = $_POST['fileName'];
    $fileSize = $_POST['fileSize'];
    $container = $_POST['container'];
    $assetId = $_POST['assetId'];
    session_start();
    $fum = new FileUploadModel();
    $fum->blockCount = $blocksCount;
    $fum->fileName = $fileName;
    $fum->fileSize = $fileSize;
    $fum->startTime = time();
    $fum->isUploadCompleted = false;
    $fum->uploadStatusMessage = '';
    $fum->container = $container;
    $fum->blockList = new BlockList();
    $fum->assetId = $assetId;
    $_SESSION['fum'] = $fum;
    echo 'true';
}
?>