<?php namespace Qdiscuss;

// just add some wp functions to process
require_once ABSPATH . '/wp-includes/pluggable.php';

use Qdiscuss\Api\Request;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Support\Helper;
use Qdiscuss\Core\Models\AccessToken;
use Qdiscuss\Dashboard\Bridge;

global $qdiscuss_endpoint, $wpdb, $action, $qdiscuss_config;

$router= app('router');
$actor = app('Qdiscuss\Core\Support\Actor');

if(!Helper::table_exists('config')) {
	$qdiscuss_endpoint = 'qdiscuss';
} else {
	$qdiscuss_endpoint = Setting::getEndPoint();
	// $actor->setUser(Helper::current_forum_user());
}

$login_with_cookie = function() use ($router, $actor){
	return function() use ($router, $actor) {
		if (($token = $router->request->cookies('qdiscuss_remember')) &&
			($accessToken = AccessToken::where('id', $token)->first())) {
			$actor->setUser($user = $accessToken->user);
			$user->updateLastSeen()->save();
		}
	};
};

$login_with_header = function() use ($router, $actor) {
	return function() use ($router, $actor) {
		$header = $router->request->headers->get('Authorization');
		// $header = $_SERVER["HTTP_AUTHORIZATION"];
		// $header = apache_request_headers()["Authorization"];
		if (starts_with($header, 'Token ') &&
			($token = substr($header, strlen('Token '))) &&
			($accessToken = AccessToken::where('id', $token)->first())) {
			$actor->setUser($user = $accessToken->user);

			$user->updateLastSeen()->save();
		} else {
			die('permission deny');
		}
	};
};

$login_with_cookie_and_check_admin = function() use ($router, $actor) {
	return function() use ($router, $actor) {
		if (($token = $router->request->cookies('qdiscuss_remember')) &&
			($accessToken = AccessToken::where('id', $token)->first()) &&
			$accessToken->user->isAdmin()) {
			$actor->setUser($accessToken->user);
		} else {
			die('permission deny');
		}

	};
};

$action = function ($class, $input=[]) use ($router, $actor) {

	$action = app($class);

	if (str_contains($router->request->headers('CONTENT_TYPE'), 'application/json') && $router->request->isPost()) {
		$input = array_merge($input, Helper::post_data());
	} elseif (str_contains($router->request->headers('CONTENT_TYPE'), 'multipart/form-data') && $router->request->isPost()) {
		$input = array_merge($input, $_FILES);
	} elseif (str_contains($router->request->headers('CONTENT_TYPE'), 'application/json') && $router->request->isPut()) {
		$input = array_merge($input,  json_decode($router->request->getBody(), true));
	} else {
		$input = array_merge($input, $router->request->params());
	}
// print_r($input);exit;
	$request = new Request($input, $actor, $router->request);
	
	header("Content-type: application/json");
	
	if ($router->request->isDelete()) {
		echo json_encode($action->handle($request)); exit;
	} else {
		// var_dump($action->handle($request));exit;
		echo json_encode($action->handle($request)->content->toArray());exit;
	}

};

$router->group('/' . $qdiscuss_endpoint, $login_with_cookie(), function() use ($router, $action, $login_with_cookie, $login_with_header, $login_with_cookie_and_check_admin){

	// // Home
	$router->get('/', function() use ($router){
		app('Qdiscuss\Forum\Actions\IndexAction')->get();exit;
	});

	// Login
	$router->post('/login', function() use ($router) {
		app('Qdiscuss\Forum\Actions\LoginAction')->post();exit;
	});

	// Logout
	$router->get('/logout', function(){
		app('Qdiscuss\Forum\Actions\LogoutAction')->get();exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Users
	|--------------------------------------------------------------------------
	*/
	// List users
	$router->get('/users' ,  function() use($action) {
		return $action('Qdiscuss\Api\Actions\Users\IndexAction');exit;
	});

	// Register a user
	$router->post('/users', $login_with_header(), function() use ($action){
		//return $action('Qdiscuss\Api\Actions\Users\CreateAction');exit;
		// return ;
	});

	// Show a single user
	$router->get('/users/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\ShowAction', compact('id'));exit;
	});

	// Edit a user
	$router->put('/users/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\UpdateAction', compact('id'));exit;
	});

	// Delete a user
	$router->delete('/users/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\DeleteAction', compact('id'));exit;
	});

	// Upload a user's avatar
	$router->post('/users/:id/avatar', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\UploadAvatarAction', compact('id'));exit;
	});

	// Delete a user's avatar
	$router->delete('/users/:id/avatar', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\DeleteAvatarAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Activity
	|--------------------------------------------------------------------------
	*/
	// List activity
	$router->get('/activity',  function() use($action) {
		return $action('Qdiscuss\Api\Actions\Activity\IndexAction');exit;
	});

	// List notifications for the current user
	$router->get('/notifications', function() use($action) {
		return $action('Qdiscuss\Api\Actions\Notifications\IndexAction');exit;
	});

	// Mark a single notification as read
	$router->put('/notifications/:id',  $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Notifications\UpdateAction', compact('id'));exit;
	});


	/*
	|--------------------------------------------------------------------------
	| Discussions
	|--------------------------------------------------------------------------
	*/
	// List discussions
	$router->get('/discussions' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Discussions\IndexAction');exit;
	});

	// Create a discussion
	$router->post('/discussions', $login_with_header(), function() use ($action) {
		return $action('Qdiscuss\Api\Actions\Discussions\CreateAction');exit;
	});

	// Show a single discussion
	$router->get('/discussions/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\ShowAction', compact('id'));exit;
	});

	// Edit a discussion
	$router->put('/discussions/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\UpdateAction', compact('id'));exit;
	});

	// Delete a discussion
	$router->delete('/discussions/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\DeleteAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Attachements
	|--------------------------------------------------------------------------
	*/
	// Create An attachment
	// Create a post
	$router->post('/attachments', $login_with_header(), $login_with_header(), function() use ($action){
		return $action('Qdiscuss\Api\Actions\Attachments\CreateAction');exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Posts
	|--------------------------------------------------------------------------
	*/
	// List posts
	$router->get('/posts' ,  function() use($action) {
		return $action('Qdiscuss\Api\Actions\Posts\IndexAction');exit;
	});

	// Create a post
	$router->post('/posts', $login_with_header(), function() use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\CreateAction');exit;
	});

	// Show a single post
	$router->get('/posts/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\ShowAction', compact('id'));exit;
	});

	// Edit a post
	$router->put('/posts/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\UpdateAction', compact('id'));exit;
	});

	// Delete a post
	$router->delete('/posts/:id', $login_with_header(), function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\DeleteAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Groups
	|--------------------------------------------------------------------------
	*/
	// List groups
	$router->get('/groups' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Groups\IndexAction');exit;
	});

	// Create a group
	$router->post('/groups', $login_with_header(), function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\CreateAction');exit;
	});

	// Show a single group
	$router->get('/groups/:id', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\ShowAction');exit;
	});

	// Edit a group
	$router->put('/groups/:id', $login_with_header(), function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\UpdateAction');exit;
	});

	// Delete a group
	$router->delete('/groups/:id', $login_with_header(), function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\DeleteAction');exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Admin
	|--------------------------------------------------------------------------
	*/
	$router->group('/admin', $login_with_cookie_and_check_admin(),  function() use ($router){
		$router->get('/', function() use ($router){
			app('Qdiscuss\Admin\Actions\IndexAction')->get();exit;
		});
	});
	
});

// Take the rest routes to wordpress
$router->notFound(function() use ($router) {
	$router->stop();
});

$router->run();
