<?php
	$filesystem = require_once __DIR__ . "/../config/filesystems.php";
	$database =  require_once __DIR__. "/../config/database.php";

	return array_merge($filesystem, $database);
