<?php

date_default_timezone_set("GMT");

if (!empty($_POST)){
    require_once 'init.php';
    $assetId = $_POST['AssetIdTextBox'];
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    
    $timeout = 30;
    $sasUrl = $asset->GetWriteAccessPath($timeout);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
<script src="/js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function publishAsset()
    {
        var slUploadControl = document.getElementById('slUploadControl');
        var dataString = 'assetId=<?=$assetId?>';
        slUploadControl.Content.SL.ChangeStatusText('Publishing asset.');
        $.ajax({
            type: "POST",
            async: true,
            url: '/ajaxupload/PublishAsset.php',
            data: dataString,
            error: function () {
                slUploadControl.Content.SL.ChangeStatusText('Error when publishing asset.');
            },
            success: function (operationState) {
                if(operationState == 'true'){
                    slUploadControl.Content.SL.ChangeStatusText('Asset has been published.');
                }else{
                    slUploadControl.Content.SL.ChangeStatusText('Error when publishing asset.');
                }
            }
        });
    }
</script>
</head>
<body>
    <div>
        <div id="silverlightControlHost">
            <object id="slUploadControl" data="data:application/x-silverlight-2," type="application/x-silverlight-2"
                width="100%" height="100%">
                <param name="source" value="silverlight/BlobUploadControl.xap" />
                <param name="onError" value="onSilverlightError" />
                <param name="background" value="white" />
                <param name="minRuntimeVersion" value="3.0.40818.0" />
                <param name="autoUpgrade" value="true" />
                <param name="initParams" value="sasUrl=<?=$sasUrl?>, timeOutInterval=<?=$timeout*60?>" />
                <a href="http://go.microsoft.com/fwlink/?LinkID=149156&v=3.0.40818.0" style="text-decoration: none">
                    <img src="http://go.microsoft.com/fwlink/?LinkId=161376" alt="Get Microsoft Silverlight"
                        style="border-style: none" />
                </a>
            </object>
            <iframe id="_sl_historyFrame" style="visibility: hidden; height: 0px; width: 0px;
                border: 0px"></iframe>
        </div>
    </div>
</body>
</html>
