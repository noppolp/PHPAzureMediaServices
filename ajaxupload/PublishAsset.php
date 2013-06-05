<?php
require_once '../classes/MediaServiceContext.php';
require_once '../init.php';

$assetId = $_POST['assetId'];
//$assetId = 'nb:cid:UUID:80e675e8-ad4f-4a92-a7cd-ee0f3e912676';
$asset = $mediaContext->getAssetReference($assetId);
$asset->Get();
$asset->Publish();
echo 'true';
?>