<?php

$app = new \Qdiscuss\Application(
	realpath(__DIR__.'/../')
);

$app->singleton('path', function(){
	return __DIR__.'/../';
});
$app->singleton('path.public', function(){
	return __DIR__.'/../public/';
});

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
