<?php

    /*
     * This file is the Application abstraction.
     * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
     * Rutgers, The State University of New Jersey
     * November 8, 2018
     */

    define("STATUS_NONE", -1);
    define("STATUS_INIT", 0);
    define("STATUS_STARTING", 1);
    define("STATUS_RUNNING", 2);
    define("STATUS_ENDED", 4);

    class App
    {
        private $id;
        private $budget;
        private $buckets;
        private $p_model;
        private $status;
        private $appDirExists;

        public function __construct()
        {
            $this->id 			= 0;
            $this->budget 		= null;
            $this->buckets 		= null;
            $this->p_model 		= null;
            $this->cost_file  = null;
            $this->mv_file		= null;
            $this->status 		= STATUS_NONE;
            $this->appDirExists = false;
        }

        public function saveToDisk($machineID)
        {
            $pmodelURL = $this->getDirectory($machineID) . '/pmodel.csv';
            if ($this->p_model !== null) {
                file_put_contents($pmodelURL, $this->p_model);
                chmod($pmodelURL, 0666);
            }

            $bucketsURL = $this->getDirectory($machineID) . '/buckets.csv';
            if ($this->buckets !== null) {
                file_put_contents($bucketsURL, $this->buckets);
                chmod($bucketsURL, 0666);
            }

            $costURL = $this->getDirectory($machineID) . '/cost.csv';
            if ($this->cost_file !== null) {
                file_put_contents($costURL, $this->cost_file);
                chmod($costURL, 0666);
            }

            $mvURL = $this->getDirectory($machineID) . '/mv.csv';
            if ($this->mv_file !== null) {
                file_put_contents($mvURL, $this->mv_file);
                chmod($mvURL, 0666);
            }

            $profileURL = $this->getDirectory($machineID) . '/profile.json';
            if (!file_exists($profileURL)) {
                file_put_contents($profileURL, json_encode($this->getEmptyProfile($machineID)));
                chmod($profileURL, 0777);
                // Now that we have our profile in place, we can generate a real profile.

                $command = '/usr/bin/python3 /home/liuliu/Research/rapid_m_backend_server/RapidMain.py' . ' --flow INIT --path2app ' . $profileURL . ' --apppfs ' . $bucketsURL . ' --appdata ' . $pmodelURL . ' --dir ' . $this->getDirectory($machineID);
                $result = exec($command, $output);
            }
        }

        public function getEmptyProfile($machineID)
        {
            $profile = array();

            $profile["CLUSTERED"] = false;
            $profile["machine_id"] = $machineID;
            $profile["name"] = $this->id;
            $profile["TRAINED"] = false;

            return $profile;
        }

        public function getDirectory($machineID)
        {
            $dirName = '/var/www/html/rapid_server/storage' . '/apps/' . $machineID . '-' . $this->id;

            // Avoid pointlessly rechecking the dir every time by only checking once per app load.
            if ($this->appDirExists == false && !file_exists($dirName)) {
                $oldmask = umask(0);
                mkdir($dirName, 0777, true);
                umask($oldmask);
            }
            $this->appDirExists = true;

            return $dirName;
        }

        public function loadFromArray($array)
        {
            $this->setID($array["id"]);
            $this->setBudget($array["budget"]);
            $this->setStatus($array["status"]);
        }

        public function setID($id)
        {
            $this->id = $id;
        }

        public function getID()
        {
            return $this->id;
        }

        public function setBudget($budget)
        {
            $this->budget = $budget;
        }

        public function getBudget()
        {
            return $this->budget;
        }

        public function setBuckets($buckets)
        {
            $this->buckets = $buckets;
        }

        public function getBuckets()
        {
            return $this->buckets;
        }

        public function setPModel($p_model)
        {
            $this->p_model = $p_model;
        }

        public function getPModel()
        {
            return $this->p_model;
        }

        public function setCost($cost)
        {
            $this->cost_file = $cost;
        }

        public function getCost()
        {
            return $this->cost_file;
        }

        public function setMV($mv)
        {
            $this->mv_file = $mv;
        }

        public function getMV()
        {
            return $this->mv_file;
        }

        public function setStatus($status)
        {
            $this->status = $status;
        }

        public function getStatus()
        {
            return $this->status;
        }

        // Converts this App into an array to be stored as JSON in a file.
        public function toArray($machineID)
        {
            $data = array();

            $data["id"] 	= $this->getID();
            $data["budget"]	= $this->getBudget();
            $data["dir"] 	= $this->getDirectory($machineID);
            $data["status"]	= $this->getStatus();

            return $data;
        }
    }
