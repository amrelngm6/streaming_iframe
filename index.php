<?php

error_reporting(0); 
// error_reporting(E_ALL); 
session_start(); 
date_default_timezone_set('Africa/Cairo');



// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// Check if installed or redirect to installation 
file_exists(__DIR__.'/app/config/database.php') 
    ?  require_once __DIR__.'/app/config/database.php' 
    : header('Location: ./installer/index.php');

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => db_host,
    'database' => db_name,
    'username' => db_username,
    'password' => db_password,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
$capsule->connection()->enableQueryLog();
$capsule->statement("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");


// Used to store actions log at the usage_log table
// Boolen:  true / false
const DEBUG_MODE = false;



/**
 * Load all System Models
 * The base folder is /app folder
 * Any Object caould be accesses through its namespace
 * All Objects inside that folder would be called
 */ 
spl_autoload_register(function ($name) {
    $name2 = str_replace('\\', '/', __DIR__.'/app/'.$name.'.php');
    is_file($name2) ? include ($name2) : '';
});



include('app/helper/methods.php');
include('app/config/route.php');
$capsule->getConnection()->disconnect();



