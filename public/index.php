<?php
// start session
session_start();
session_gc();

// require the autoloader
require __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/autoload.php';

// load the config file
require_once __DIR__.'/../app/Config/config.php';



// create the application
$app = require_once __DIR__. '/../app/app.php';

/**
 * Require the routes that will be used in this application
 */
require_once dirname(__DIR__). '/routes/web.php';


// Start the application
$app->start();


