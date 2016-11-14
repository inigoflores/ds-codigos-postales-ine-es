<?php

require_once "vendor/autoload.php";
require_once('lib/Config.php');

define('DS','/');
define ('BASE_PATH', dirname(__DIR__) . DS . "..");

// Comprobamos si los directorios están creados. Si no lo están, los creamos
is_dir(BASE_PATH . DS . Config::DATA_FOLDER) || mkdir(BASE_PATH . DS . Config::DATA_FOLDER);
is_dir(BASE_PATH . DS . Config::ARCHIVE_FOLDER) || mkdir(BASE_PATH . DS . Config::ARCHIVE_FOLDER);


// Inicializamos consola

$console = new ConsoleKit\Console();
$console->addCommandsFromDir("lib",null,true);

if (sizeof($argv)==1) {
    $console->run(['download']);
    $console->run(['process']);
    $console->run(['convert-to-json']);
    $console->run(['update']);
} else {
    $console->run();
}

