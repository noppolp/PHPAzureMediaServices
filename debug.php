<?php
require_once '/classes/MediaServiceContext.php';

$accountName = 'noppolmedia';
$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
$storageAccountName = 'noppolpstorage';
$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
$mediaContext->checkForRedirection();


$asset = $mediaContext->getAssetReference('nb:cid:UUID:c01df732-1a8f-45a7-a10c-591229587c89');
$asset->Get();
$locs = $asset->ListLocators();
foreach($locs as $loc){
    $acc = $mediaContext->getAccessPolicyReference($loc->accessPolicyId);
    $acc->Get();
    if($acc->permissions == AccessPolicyPermission::$WRITE){
        $acc->Delete();
        $loc->Delete();
    }
}
?>