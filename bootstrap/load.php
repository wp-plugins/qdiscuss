<?php

define("QD_PREFIX", 'qd_');//just ugly, for change later :(

/**
 * Get db from illuminate
 */
global $qdiscuss_db, $qdiscuss_config;

$qdiscuss_config = require_once __DIR__.'/config.php';

$qdiscuss_db = require_once __DIR__.'/db.php';