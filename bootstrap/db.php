<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

global $wpdb, $qdiscuss_config, $qdiscuss_db;

$database_config = $qdiscuss_config['database'];

$capsule = new Capsule;
$capsule->addConnection($database_config);
$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();
return $qdiscuss_db = $capsule;