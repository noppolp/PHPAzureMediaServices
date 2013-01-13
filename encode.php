<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Asset Id: <input type="text" id="AssetIdTextBox" name="AssetIdTextBox" class="field" size="50"/><br />
    Encode Preset: <input type="text" id="EPresetTextBox" name="EPresetTextBox" value="H264 Broadband SD 16x9" class="field" size="50"/><br />
    MP4 Output Name: <input type="text" id="MP4NameTextBox" name="MP4NameTextBox" value="MP4 Output" class="field"/><br />
    Thumbnails Output Name: <input type="text" id="ThumbnailsNameTextBox" name="ThumbnailsNameTextBox" value="Thumbnails Output" class="field"/><br />
    <input type="submit" value="Encode" />
</form>
<?php
require_once '/classes/MediaServiceContext.php';

$accountName = 'noppolmedia';
$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
$storageAccountName = 'noppolpstorage';
$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
$mediaContext->checkForRedirection();

if (!empty($_POST)){
    $assetId = $_POST['AssetIdTextBox'];
    $mp4OutputName = $_POST['MP4NameTextBox'];
    $thumbnailsOutputName = $_POST['ThumbnailsNameTextBox'];
    $encodePreset = $_POST['EPresetTextBox']; 
    
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $mediaProcessor = $mediaContext->getMediaProcessor('Windows Azure Media Encoder');
    $job = $mediaContext->getJobReferenceByName('Encode to MP4 Job');
    $encodeToMP4Task = $job->AddNewTask('Encode to MP4', $mediaProcessor, $encodePreset);
    $encodeToMP4Task->AddInputMediaAsset($asset);
    $encodeToMP4Task->AddNewOutputMediaAsset($mp4OutputName);
    $thumbnailTask = $job->AddNewTask('Create Thumbnails', $mediaProcessor, 'Thumbnails');
    $thumbnailTask->AddInputMediaAsset($encodeToMP4Task->outputMediaAssets[0]);
    $thumbnailTask->AddNewOutputMediaAsset($thumbnailsOutputName);
    $job->Submit();
    
    echo '<b>JOB ID: </b>' . $job->id;
}
?>