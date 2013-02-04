<html>
<head>
<title>test upload
</title>

<script type="text/javascript" src="jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="ajax_upload.js"></script>

</head>
<form id="form1" runat="server">
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
                        <input type="button" id="upload" name="Upload" value="Upload" onclick="startUpload('FileInput', 'uploadProgress', 'statusMessage', 'upload', 'cancel');"
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
</html>