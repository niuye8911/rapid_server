<?php

    /*
     * This file is the CHECK API Node.
     * Liu Liu (jack.liuliu@cs.rutgers.edu)
     * Rutgers, The State University of New Jersey
     * Oct. 2019
     */


    require_once("class/Autoload.php");

    $argParser 	= new ArgumentParser();

    $machineID 	= $argParser->getMachineID();
    $appID		= $argParser->getApplicationID();

    $fileParser	= new FileParser($machineID);


    // When app calls check, only give back the most recent result:

    $bucketSelection = $fileParser->getBucket($appID);
    echo json_encode($bucketSelection);
