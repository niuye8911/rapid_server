<?php

	/*
	 * This file is the Init API Node.
	 * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
	 * Rutgers, The State University of New Jersey
	 * November 8, 2018
	 */


	require_once("class/Autoload.php");

	$argParser 	= new ArgumentParser();

	$machineID 	= $argParser->getMachineID();
	$appID		= $argParser->getApplicationID();

	$fileParser	= new FileParser($machineID);


	// When app calls init, gives 2 CSVs :
		// 1) raw data for all config system profile clustering
		// 2) raw data for perf. model training
	//

	$app->setID($argParser->getApplicationID()); //
	$app->setBudget($argParser->getBudget()); //
	$app->setBuckets($argParser->getBuckets()); // May have to generate ourselves if not provided
	$app->setPModel($argParser->getPModel()); // example_app_measurement.csv
	$app->setStatus(STATUS_INIT);
	echo "Finished app setting";
	$fileParser->addApplication($app);
	echo "finished add application";

	$fileParser->saveToDisk();
	echo "finished saving to disk";
?>
