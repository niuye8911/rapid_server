<?php

	/*
	 * This file is the File Parser.
	 *
	 * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
	 * Rutgers, The State University of New Jersey
	 * November 8, 2018
	 */

	// TODO: find out how to restrict reads of file during a write phase.
	// https://secure.php.net/manual/en/function.flock.php

	class FileParser{

		private $machineID;				// The ID of the machine we are using, used to find the file.
		private $applications;			// An array of App objects.
		private $updateComputeNeeded; 	// Determines whether or not we need to trigger an update of COMPUTE.
		private $currentCompute;		// The current result of the COMPUTE function.
		private $lastLoad;				// When the file was last accessed.

		function __construct($machineID){
			$this->machineID = $machineID;
			$this->loadApplications();
			$this->updateComputeNeeded = false;
			$this->lastLoad = 0;
			$this->currentCompute = null;
			dbg(2, count($this->applications) . " applications loaded");
		}

		public function getApplications(){ return $this->applications; }

		private function getFileName(){ return "storage/data_machine_" . $this->machineID . '.txt'; }

		public function getApplicationByID($id){
			foreach($this->applications as $app){
				if($app->getID() == $id) return $app;
			}

			return null;
		}

		public function updateApplication($app, $newStatus, $newBudget){
			$app->setStatus($newStatus);
			$app->setBudget($newBudget);

			$this->updateComputeNeeded = true;
		}

		public function getBucket($app){
			return 1; // TODO: Actually implement.
		}

		private function getCompute(){
			if($this->updateComputeNeeded) compute();

			return $this->currentCompute;
		}

		// Triggers the call to COMPUTE.
		private function compute(){
			$this->currentCompute = "I set my value!";

			$this->updateComputeNeeded = false; // We no longer need to update compute :)
		}

		public function addApplication($app){
			$this->applications[$app->getID()] = $app;
		}

		private function loadApplications(){
			$this->applications = array();

			if(!file_exists($this->getFileName())) return; // We are done, there's no apps :)
			dbg(3, "File has contents!");

			$contents = file_get_contents($this->getFileName());

			$data = json_decode($contents, true);
			foreach($data["applications"] as $appArray){
				$app = new App();
				$app->loadFromArray($appArray);
				$this->applications[$app->getID()] = $app;
			}

			$this->lastLoad = $data["lastLoad"];
			$this->currentCompute = $data["compute"];
		}

		private function getAppsJSON(){
			$data = array();

			$data["applications"] = array();
			foreach($this->applications as $app){
				$data["applications"][] = $app->toArray($this->machineID);
			}

			$data["lastload"] 	= time();
			$data["compute"] 	= $this->currentCompute;

			return json_encode($data, JSON_PRETTY_PRINT);
		}

		public function saveToDisk(){
			foreach($this->applications as $app){
				$app->saveToDisk($this->machineID);
			}

			$json = $this->getAppsJSON();

			dbg(3, "Saving to disk: " . $json);

			$status = file_put_contents($this->getFileName(), $json);
			if(!$status) chmod($this->getFileName(), 0666);
			if(!$status) dbg(1, "File saving failed");
		}

	}

?>
