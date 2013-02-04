<?php

move_uploaded_file($_FILES["Slice"]["tmp_name"], "upload/" . $_FILES["file"]["name"].$_GET["block_id"]);

echo "block id ".$_GET["block_id"];

?>