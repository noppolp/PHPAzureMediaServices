<?php
//require_once '/classes/MediaServiceContext.php';

//$accountName = 'noppolmedia';
//$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
//$storageAccountName = 'noppolpstorage';
//$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
//$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
//$mediaContext->checkForRedirection();

$padInt = sprintf('%06d', 12);
echo $padInt;
echo '<br/>' . base64_encode($padInt);

echo '<br/>' . gmdate("D, d M Y H:i:s") . ' GMT';
?>
DEBUG
DEBUG
Hello World
