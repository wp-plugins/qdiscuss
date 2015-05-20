<?php namespace Qdiscuss;

// just add some wp functions to process
require_once ABSPATH . '/wp-includes/pluggable.php';

use Slim\Slim;
use Qdiscuss\Api\Request;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Support\Helper;
use Qdiscuss\Core\Models\AccessToken;
use Qdiscuss\Dashboard\Bridge;

$slim_app = new Slim;

global $qdiscuss_endpoint, $qdiscuss_actor, $wpdb, $action;

$config_table_name = $wpdb->prefix . 'qd_' . 'config';
if($wpdb->get_var("SHOW TABLES LIKE '$config_table_name'") != $config_table_name) {
	$qdiscuss_endpoint = 'qdiscuss';
} else {
	$qdiscuss_endpoint = Setting::getEndPoint();
	$app = new \Qdiscuss;
	$app->init();
	$qdiscuss_actor->setUser(Helper::current_forum_user());
	// \Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
}

$login_with_cookie = function() use ($slim_app, $qdiscuss_actor){
	return function() use ($slim_app, $qdiscuss_actor){
		if (($token = $slim_app->request->cookies('qdiscuss_remember')) &&
			($accessToken = AccessToken::where('id', $token)->first())) {
			$qdiscuss_actor->setUser($user = $accessToken->user);
			$user->updateLastSeen()->save();
		}
	};
};

$action = function ($class, $input=[]) use ($slim_app) {

	global $qdiscuss_app, $qdiscuss_actor;
	$action = app($class);

	if (str_contains($slim_app->request->headers('CONTENT_TYPE'), 'application/json') && $slim_app->request->isPost()) {
		$input = array_merge($input, Helper::post_data());
	} elseif (str_contains($slim_app->request->headers('CONTENT_TYPE'), 'multipart/form-data') && $slim_app->request->isPost()) {
		$input = array_merge($input, $_FILES);
	} elseif (str_contains($slim_app->request->headers('CONTENT_TYPE'), 'application/json') && $slim_app->request->isPut()) {
		$input = array_merge($input,  json_decode($slim_app->request->getBody(), true));
	} else {
		$input = array_merge($input, $slim_app->request->params());
	}
// print_r($input);exit;
	$request = new Request($input, $qdiscuss_actor, $slim_app->request);
	
	header("Content-type: application/json");
	
	if ($slim_app->request->isDelete()) {
		echo json_encode($action->handle($request)); exit;
	} else {
		echo json_encode($action->handle($request)->content->toArray());exit;
	}

};

$slim_app->group('/' . $qdiscuss_endpoint, function() use ($slim_app, $action, $login_with_cookie){

	// Home
	$slim_app->get('/',  function() use ($slim_app){
		$action = new \Qdiscuss\Forum\Actions\IndexAction;
		$action->get();
		exit;
	});

	// Login
	$slim_app->post('/login', function() use ($slim_app) {
		$action = new \Qdiscuss\Forum\Actions\LoginAction;
		$action->post();
		exit;
	});

	// Logout
	$slim_app->get('/logout', function(){
		$action = new \Qdiscuss\Forum\Actions\LogoutAction;
		$action->get();
		exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Users
	|--------------------------------------------------------------------------
	*/
	// List users
	$slim_app->get('/users' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Users\IndexAction');exit;
	});

	// Register a user
	$slim_app->post('/users', function() use ($action){
		//return $action('Qdiscuss\Api\Actions\Users\CreateAction');exit;
		return ;
	});

	// Show a single user
	$slim_app->get('/users/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\ShowAction', compact('id'));exit;
	});

	// Edit a user
	$slim_app->put('/users/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\UpdateAction', compact('id'));exit;
	});

	// Delete a user
	$slim_app->delete('/users/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\DeleteAction', compact('id'));exit;
	});

	// Upload a user's avatar
	$slim_app->post('/users/:id/avatar', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\UploadAvatarAction', compact('id'));exit;
	});

	// Delete a user's avatar
	$slim_app->delete('/users/:id/avatar', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Users\DeleteAvatarAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Activity
	|--------------------------------------------------------------------------
	*/
	// List activity
	$slim_app->get('/activity' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Activity\IndexAction');exit;
	});

	// List notifications for the current user
	$slim_app->get('/notifications' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Notifications\IndexAction');exit;
	});

	// Mark a single notification as read
	$slim_app->put('/notifications/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Notifications\UpdateAction', compact('id'));exit;
	});


	/*
	|--------------------------------------------------------------------------
	| Discussions
	|--------------------------------------------------------------------------
	*/
	// List discussions
	$slim_app->get('/discussions' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Discussions\IndexAction');exit;
	});

	// Create a discussion
	$slim_app->post('/discussions', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\CreateAction');exit;
	});

	// Show a single discussion
	$slim_app->get('/discussions/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\ShowAction', compact('id'));exit;
	});

	// Edit a discussion
	$slim_app->put('/discussions/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\UpdateAction', compact('id'));exit;
	});

	// Delete a discussion
	$slim_app->delete('/discussions/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Discussions\DeleteAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Posts
	|--------------------------------------------------------------------------
	*/
	// List posts
	$slim_app->get('/posts' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Posts\IndexAction');exit;
	});

	// Create a post
	$slim_app->post('/posts', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\CreateAction');exit;
	});

	// Show a single post
	$slim_app->get('/posts/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\ShowAction', compact('id'));exit;
	});

	// Edit a post
	$slim_app->put('/posts/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\UpdateAction', compact('id'));exit;
	});

	// Delete a post
	$slim_app->delete('/posts/:id', function($id) use ($action){
		return $action('Qdiscuss\Api\Actions\Posts\DeleteAction', compact('id'));exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Groups
	|--------------------------------------------------------------------------
	*/
	// List groups
	$slim_app->get('/groups' , function() use($action) {
		return $action('Qdiscuss\Api\Actions\Groups\IndexAction');exit;
	});

	// Create a group
	$slim_app->post('/groups', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\CreateAction');exit;
	});

	// Show a single group
	$slim_app->get('/groups/:id', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\ShowAction');exit;
	});

	// Edit a group
	$slim_app->put('/groups/:id', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\UpdateAction');exit;
	});

	// Delete a group
	$slim_app->delete('/groups/:id', function() use ($action){
		return $action('Qdiscuss\Api\Actions\Groups\DeleteAction');exit;
	});

	/*
	|--------------------------------------------------------------------------
	| Admin
	|--------------------------------------------------------------------------
	*/
	$slim_app->group('/admin', function() use ($slim_app){
		$slim_app->get('/', function() use ($slim_app){
			$action = new \Qdiscuss\Admin\Actions\IndexAction;
			$action->get();
			exit;
		});
	});
	
});

// Take the rest routes to wordpress
$slim_app->notFound(function() use ($slim_app) {
	$slim_app->stop();
});

$slim_app->run();