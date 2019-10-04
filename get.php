<?php

    /*
     * This file is the Get API Node.
     * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
     * Rutgers, The State University of New Jersey
     * November 8, 2018
     */

    require_once("class/Autoload.php");

    $argParser 	= new ArgumentParser();

    $machineID 	= $argParser->getMachineID();
    $appID		= $argParser->getApplicationID();
    $budget = $argParser->getBudget();

    $fileParser	= new FileParser($machineID);

    // check if the app has been inited
    $app = $fileParser->getApplicationByID($argParser->getApplicationID());
    if ($app == null) {
        echo "Error";
        die();
    }
    // update the app's budget
    $fileParser->updateApplication($app, STATUS_RUNNING, $budget);
    $fileParser->saveToDisk();

    // re-calculate and return the bucket selection
    $bucketSelection = $fileParser->getBucket($appID);
    
    echo json_encode($bucketSelection);
