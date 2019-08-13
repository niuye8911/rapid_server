<?php

require_once("class/Autoload.php");

$fileParser	= new FileParser('algaesim');

// reset all apps
foreach ($fileParser->getApplications() as $app) {
    $fileParser->updateApplication($app->getID(), STATUS_INIT, 0);
}

echo "ALL Apps Reset";

// remove the result file
file_put_contents('./storage/data_machine_algaesim_result.txt', '');

$fileParser->saveToDisk();
