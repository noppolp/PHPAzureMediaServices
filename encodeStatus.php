<form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    Job Id: <input type="text" id="JobIdTextBox" name="JobIdTextBox" class="field" size="50"/>
    <input type="submit" value="See Status" />
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
    $jobId = $_POST['JobIdTextBox'];
    
    $job = $mediaContext->getJobReference($jobId);
    $job->Get();
    echo '<br/> <b>Job Name: </b> ' . $job->name;
    echo '<br/> <b>Job State: </b> ' . JobState::GetJobStateString($job->state);
    
    $tasks = $job->ListTasks();
    echo '<p>';
    foreach($tasks as $task){
        echo '<br/><b>Task Name:</b> ' . $task->name . ' ___________ <b>Progress:</b> ' . $task->progress;
    }
    echo '</p>';
    
    $assets = $job->ListOutputAssets();
    echo '<p>';
    foreach($assets as $asset){
        echo '<br/><b>Output Asset ID: </b>' . $asset->id;
    }
    echo '</p>';
}
?>