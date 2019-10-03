<?php

    /*
     * This file is the Start API Node.
     * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
     * Rutgers, The State University of New Jersey
     * November 8, 2018
     */

    require_once("class/Autoload.php");

    $argParser 	= new ArgumentParser();

    $machineID = $argParser->getMachineID();
    $appID = $argParser->getApplicationID();
    $newBudget = $argParser->getBudget();
    $fileParser	= new FileParser($machineID);
    # check if app is inited
    $app = $fileParser->getApplicationByID($argParser->getApplicationID());
    if ($app == null) {
        echo "Error";
        die();
    }
    # update the problem
    $fileParser->updateApplication($app->getID(), STATUS_STARTING, $newBudget);
    $fileParser->saveToDisk();
    # solve the updated problem
    $bucketSelection = $fileParser->getBucket($appID);
    # return the result
    echo json_encode($bucketSelection);
