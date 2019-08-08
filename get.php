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

    $fileParser	= new FileParser($machineID);

    $app = $fileParser->getApplicationByID($argParser->getApplicationID());
    if ($app == null) {
        echo "Error";
        die();
    }

    echo $fileParser->getBucket($app);


    $fileParser->saveToDisk();
