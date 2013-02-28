<?php
require_once 'classes\vendor\autoload.php';
require_once 'config.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
require_once 'classes\MediaServiceContext.php';

global $mediaContext;

$accountName = AzureInfo::$accountName;
$accountKey = AzureInfo::$accountKey;
$storageAccountName = AzureInfo::$storageAccountName;
$storageAccountKey = AzureInfo::$storageAccountKey;
$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
$mediaContext->checkForRedirection();
?>