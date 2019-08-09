<?php

    /*
     * This file is the Argument Parser.
     * This file will handle loading in all parameters given by the user and processing them as needed.
     * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
     * Rutgers, The State University of New Jersey
     * November 9, 2018
     */

    define("KEY_MACHINE", "machine");
    define("KEY_APP", "app");
    define("KEY_BUCKETS", "buckets");
    define("KEY_BUDGET", "budget");
    define("KEY_PMODEL", "p_model");
    define("KEY_COST", "cost");
    define("KEY_MV", "mv");

    class ArgumentParser
    {
        private $machineID = null;	// The ID of the machine we are using, used to find the file.
        private $applicationID = null;	// The ID of the application we are targeting.
        private $buckets = null;
        private $p_model = null;
        private $budget = null;

        public function __construct()
        {
        }

        // Returns $_GET[$key] if it is set, or $fallback otherwise.
        private function getGet($key, $fallback)
        {
            if (!isset($_GET[$key])) {
                return $fallback;
            }
            return $_GET[$key];
        }

        // Returns $_POST[$key] if it is set, or $fallback otherwise.
        private function postGet($key, $fallback)
        {
            if (!isset($_POST[$key])) {
                return $fallback;
            }
            return $_POST[$key];
        }

        public function getMachineID()
        {
            if ($this->machineID == null) {
                $this->machineID = $this->getGet(KEY_MACHINE, "err");
            }
            return $this->machineID;
        }

        public function getApplicationID()
        {
            if ($this->applicationID == null) {
                $this->applicationID = $this->getGet(KEY_APP, "err");
            }
            return $this->applicationID;
        }

        public function getPModel()
        {
            if ($this->p_model == null) {
                $this->p_model = $this->postGet(KEY_PMODEL, array());
            }
            return $this->p_model;
        }

        public function getMV()
        {
            if ($this->mv_file == null) {
                $this->mv_file = $this->postGet(KEY_MV, array());
            }
            return $this->mv_file;
        }

        public function getCost()
        {
            if ($this->cost_file == null) {
                $this->cost_file = $this->postGet(KEY_COST, array());
            }
            return $this->cost_file;
        }

        public function getBuckets()
        {
            if ($this->buckets == null) {
                $this->buckets = $this->postGet(KEY_BUCKETS, array());
            }
            return $this->buckets;
        }

        public function getBudget()
        {
            if ($this->budget == null) {
                $this->budget = $this->postGet(KEY_BUDGET, 0);
            }
            return $this->budget;
        }
    }
