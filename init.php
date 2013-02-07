<?php
require_once 'classes\vendor\autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
require_once 'classes\MediaServiceContext.php';

global $blobRestProxy, $mediaContext;

$accountName = 'noppolmedia';
$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
$storageAccountName = 'noppolpstorage';
$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
$mediaContext->checkForRedirection();

$connectionString = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', $storageAccountName, $storageAccountKey);
$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
?>