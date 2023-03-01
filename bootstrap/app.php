<?php
/**
 * Create a new application instance
 */
$app = new App\Application();

/**
 * Register the application bindings
 */
 $app->singleton(
   App\Http\HttpKernel\Kernel::class,
   App\Http\HttpKernel\Kernel::class
 );

 $app->singleton(
   App\Http\HttpKernel\Router::class,
   App\Http\HttpKernel\Router::class
 );

$loader = new \Twig\Loader\FilesystemLoader('../resources/views');
$twig = new \Twig\Environment($loader, [
    "cache" => false
]);
$twig->addExtension(new \App\Support\GlobalFunctionsTwig());

$app->singleton(
    'twig',
    $twig);

/**
 * Register an exception handler
 * This will dump and die any exceptions
 * so that you don't have to look in the console for the exception message
 */
set_exception_handler(function($exception) {

  error($exception);
});

//set_error_handler(function ($error) {
//  dump('waar?');
//  error($error);
//});

/**
 * Return the application instance
 * @return App\Application
 */
return $app;