<?php
if (!empty($_POST)){
    require_once 'init.php';
    $assetId = $_POST['AssetIdTextBox'];
    $asset = $mediaContext->getAssetReference($assetId);
    $asset->Get();
    $blobContainer = $asset->GetStorageContainerName();
}
?>
<html>
<head>
        <style type="text/css">
        html
        {
            height: 100%;
        }
        body
        {
            padding: 0;
            margin: 0;
            width: 100%;
        }
        .divHeader
        {
            background-color: #003366;
            vertical-align: middle;
            height: 50px;
            width: 100%;
            color: White;
            font-family: Microsoft Sans Serif;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }
        
        .divBody
        {
            vertical-align: middle;
            margin-left: auto;
            margin-right: auto;
        }
        
        .fileInput
        {
            height: 22px;
            width: 100%;
            color: White;
            background-color: Transparent;
            border: solid 1px black;
        }
        
        .message
        {
            font-size: 12px;
            font-family: Arial;
            color: White;
        }
        
        #controlHost
        {
            border: 3px solid #293955;
            padding: 7px 7px 7px 7px;
            width: 400px;
            height: 130px;
        }
        
        #outerPanel
        {
            background: #293955;
            height: 100%;
            width: 100%;
            vertical-align: middle;
        }
        
        input.button
        {
            color: #050;
            height: 22px;
            font-style: normal;
            font-family: Arial;
            background-color: White;
            color: Black;
            border: 1px solid black;
        }
    </style>
    <script src="/js/jquery-1.5.1.min.js" type="text/javascript"></script>
    <script src="/js/BlockBlobUpload.js"  type="text/javascript"></script>
</head>
<body>
<form id="form1" runat="server">
<table class="divHeader">
    <tr>
        <td>
            HTML5 Upload
        </td>
    </tr>
</table>
<div align="center" class="divBody">
    <br />
    <div id="controlHost">
        <div id="outerPanel">
            <table width="100%" cellpadding="2" cellspacing="5">
                <tr align="left">
                    <td colspan="2">
                        <span class="message">Please select a file to upload</span>
                    </td>
                </tr>
                <tr align="left">
                    <td valign="top">
                        <input type="file" id="FileInput" multiple="false" class="fileInput" />
                    </td>
                    <td align="right">
                        <input type="button" id="upload" name="Upload" value="Upload" onclick="startUpload('FileInput', 1048576, 'uploadProgress', 'statusMessage', 'upload', 'cancel', '<?=$assetId?>', '<?=$blobContainer?>');"
                            class="button" />
                        <input type="button" id="cancel" name="Cancel" value="Cancel" disabled="disabled"
                            onclick="cancelUpload();" class="button" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <progress id="uploadProgress" value="0" max="100" style="width: 100%; height: 10px"
                            hidden="hidden" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left">
                        <span id="statusMessage" class="message"></span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</form>
</body>
</html>