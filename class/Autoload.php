<?php

    /*
     * This file loads all required PHP files.
     * Joseph A. Boyle (joseph.a.boyle@rutgers.edu)
     * Rutgers, The State University of New Jersey
     * November 9, 2018
     */

    require_once("App.php");
    require_once("FileParser.php");
    require_once("ArgumentParser.php");
    require_once("Result.php");

    /*
     * Debug levels:
     *	0: none
     *  1: Errors only
     *  2: Verbose
     * 	3: All
     */
    define("DEBUG", 3);

    function dbg($level, $msg)
    {
        if (DEBUG < $level) {
            return;
        }
        echo $msg . '<br>';
    }
