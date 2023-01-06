<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @var App\Application $app
 */
$app = require_once __DIR__ . '/../bootstrap/app.php';

require_once __DIR__ . '/../app/Support/helpers.php';

$app->boot();
$httpKernel = $app->get(App\Http\HttpKernel\Kernel::class);

try {
    $httpKernel->handle(
        $request = App\Http\HttpKernel\Request::capture()
    )->send();
} catch (Exception $e) {
    dd($e->getMessage());
}
