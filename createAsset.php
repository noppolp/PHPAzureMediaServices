<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Name: <input type="text" id="NameTextBox" name="NameTextBox" class="field"/>
    <input type="submit" value="Create" />
</form>

<?php
require_once '/classes/MediaServiceContext.php';
require_once 'init.php';

//$accountName = 'noppolmedia';
//$accountKey = 'qhWqMa04+fQcI9MI4iGKiUph7m/LKus9EJwUHq6tNFk=';
//$storageAccountName = 'noppolpstorage';
//$storageAccountKey = 'LJuxLFsD9/4igqJvLQkYDerYQImZVG6KCdigRwwczWrRbQBvaQuw9rziahn3QXhrTjixXZ7CpRTMO8zq18BmTg==';
//$mediaContext = new MediaServiceContext($accountName, $accountKey, $storageAccountName, $storageAccountKey);
//$mediaContext->checkForRedirection();

$assets = $mediaContext->ListAssets();
foreach($assets as $asset){
    echo '<b>ID:</b> ' . $asset->id . ' __________ <b>NAME:</b> ' . $asset->name . '<br/>';
}

if (!empty($_POST)){
    $asset = $mediaContext->getAssetReference();
    $asset->name = $_POST['NameTextBox'];
    $asset->Create();
    echo '<br/> New Created Asset <b>ID:</b> ' . $asset->id . ' __________ <b>NAME:</b> ' . $asset->name . '<br/>';
}
?>