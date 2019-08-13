<?php

require_once("class/Autoload.php");

$fileParser	= new FileParser('algaesim');

$app = $fileParser->getApplicationByID("swaptions");

if ($app == null) {
    echo "Error";
    die();
}

$newBudget = 0;
$fileParser->updateApplication("swaptions", STATUS_ENDED, $newBudget);

echo "SUCCESS";

$fileParser->saveToDisk();
