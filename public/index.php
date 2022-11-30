<?php
// start session
session_start();
session_gc();

// require the autoloader
require __DIR__.'/../vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

// create the application
$app = require_once __DIR__. '/../app/app.php';

/**
 * Require the routes that will be used in this application
 */
require_once dirname(__DIR__). '/routes/web.php';

// Require some helper functions
require_once dirname(__DIR__) . '/app/Helpers/Functions/functions.php';

// Start the application
$app->start();



