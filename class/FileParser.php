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

    class FileParser
    {
        private $machineID;				// The ID of the machine we are using, used to find the file.
        private $applications;			// An array of App objects.
        private $updateComputeNeeded; 	// Determines whether or not we need to trigger an update of COMPUTE.
        private $currentCompute;		// The current result of the COMPUTE function.
        private $lastLoad;				// When the file was last accessed.
        private $result;

        public function __construct($machineID)
        {
            $this->machineID = $machineID;
            $this->loadApplications();
            $this->updateComputeNeeded = false;
            $this->lastLoad = 0;
            $this->result=null;
            $this->currentCompute = "/var/www/html/rapid_server/storage/data_machine_".$this->machineID . '_result.txt';
            dbg(3, count($this->applications) . " applications loaded");
        }

        public function getApplications()
        {
            return $this->applications;
        }

        private function getFileName()
        {
            return "/var/www/html/rapid_server/storage/data_machine_" . $this->machineID . '.txt';
        }

        private function getResultFileName()
        {
            $resultURL = $this->currentCompute;
            if (!file_exists($resultURL)) {
                file_put_contents($resultURL, '');
                chmod($resultURL, 0777);
            }
            return $resultURL;
        }

        public function getApplicationByID($id)
        {
            foreach ($this->applications as $app) {
                if ($app->getID() == $id) {
                    return $app;
                }
            }
            return null;
        }

        // update application will only be called if:
        // 1) new app comes in
        // 2) app goes away
        // 3) app needs to recompute
        public function updateApplication($app_id, $newStatus, $newBudget)
        {
            foreach ($this->applications as $app) {
                if ($app->getID() == $app_id) {
                    $app->setStatus($newStatus);
                    $app->setBudget($newBudget);
                    $this->applications[$app->getID()] = $app;
                }
            }
            $this->updateComputeNeeded = true;
        }

        public function getBucket($appID)
        {
            if ($this->updateComputeNeeded) {
                $this->compute();
            }
            $this->readResult();
            return $this->result->getAppResult($appID);
        }

        // read the result file
        private function readResult()
        {
            $this->result = new Result($this->currentCompute);
        }

        // Triggers the call to COMPUTE.
        private function compute()
        {
            $command = '/usr/bin/python3 /home/liuliu/Research/rapid_m_backend_server/RapidMain.py' . ' --flow GET_BUCKETS --apps ' . $this->getFileName() . ' --result '.$this->getResultFileName();
            $exec_result = shell_exec($command);
            $this->updateComputeNeeded = false; // We no longer need to update compute :)
        }

        public function addApplication($app)
        {
            $this->applications[$app->getID()] = $app;
        }

        private function loadApplications()
        {
            $this->applications = array();

            if (!file_exists($this->getFileName())) {
                return;
            } // We are done, there's no apps :)
            dbg(3, "File has contents!");

            $contents = file_get_contents($this->getFileName());

            $data = json_decode($contents, true);
            foreach ($data["applications"] as $appArray) {
                $app = new App();
                $app->loadFromArray($appArray);
                $this->applications[$app->getID()] = $app;
            }

            $this->lastLoad = $data["lastload"];
            $this->currentCompute = $data["compute"];
        }

        private function getAppsJSON()
        {
            $data = array();

            $data["applications"] = array();
            foreach ($this->applications as $app) {
                $data["applications"][] = $app->toArray($this->machineID);
            }

            $data["lastload"] 	= time();
            $data["compute"] 	= $this->currentCompute;

            return json_encode($data, JSON_PRETTY_PRINT);
        }

        public function saveToDisk()
        {
            foreach ($this->applications as $app) {
                $app->saveToDisk($this->machineID);
            }

            $json = $this->getAppsJSON();

            dbg(3, "Saving to disk: " . $json);

            $status = file_put_contents($this->getFileName(), $json);
            if (!$status) {
                chmod($this->getFileName(), 0666);
            }
            if (!$status) {
                dbg(1, "File saving failed");
            }
        }
    }
