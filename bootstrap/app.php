<?php

$app = new \Qdiscuss\Application(
	realpath(__DIR__.'/../')
);

// print_r($app);exit;

$app->singleton('path', function(){
	return __DIR__.'/../';
});
$app->singleton('path.public', function(){
	return __DIR__.'/../public/';
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/
// $app->middleware([
//     // 'Illuminate\Cookie\Middleware\EncryptCookies',
//     // 'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
//     // 'Illuminate\Session\Middleware\StartSession',
//     // 'Illuminate\View\Middleware\ShareErrorsFromSession',
//     // 'Laravel\Lumen\Http\Middleware\VerifyCsrfToken',
// ]);
// $app->routeMiddleware([
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
$app->register('Qdiscuss\Forum\ForumServiceProvider');
$app->register('Qdiscuss\Admin\AdminServiceProvider');
$app->register('Qdiscuss\Api\ApiServiceProvider');
$app->register('Qdiscuss\Core\CoreServiceProvider');
$app->register('Qdiscuss\Support\Extensions\ExtensionsServiceProvider');
