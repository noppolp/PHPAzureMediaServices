<?php
    require_once 'classes\vendor\autoload.php';
    require_once 'config.php';
    use WindowsAzure\Common\ServicesBuilder;
    use WindowsAzure\Common\ServiceException;

    global $tableRestProxy, $blobRestProxy, $queueRestProxy;

    $storageAccountName = AzureInfo::$storageAccountName;
    $storageAccountKey = AzureInfo::$storageAccountKey;
    $connectionString = 'DefaultEndpointsProtocol=https;AccountName='. $storageAccountName .';AccountKey=' . $storageAccountKey;
    
    $tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);
    $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
    $queueRestProxy = ServicesBuilder::getInstance()->createQueueService($connectionString);
?>