<?php

use Illuminate\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Qdiscuss\Support\ServiceProvider;
use Qdiscuss\Core\Formatter\FormatterManager;
use Qdiscuss\Core\Models\CommentPost;
use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Models\BaseModel;
use Qdiscuss\Core\Models\Forum;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Search\GambitManager;
use League\Flysystem\Adapter\Local;
use Qdiscuss\Core\Events\RegisterDiscussionGambits;
use Qdiscuss\Core\Events\RegisterUserGambits;
use Qdiscuss\Extend\Permission;
use Qdiscuss\App;

class Qdiscuss extends App {
	
	public  function init()
	{
		global $qdiscuss_app, $qdiscuss_bus, $qdiscuss_event, $qdiscuss_gambits, $qdiscuss_actor, $qdiscuss_formatter, $qdiscuss_title, $qdiscuss_desc, $qdiscuss_endpoint;

		$qdiscuss_app = new \Illuminate\Container\Container;
		$qdiscuss_bus = new \Illuminate\Bus\Dispatcher($qdiscuss_app);
		$qdiscuss_event = new \Illuminate\Events\Dispatcher;
		$qdiscuss_actor = new \Qdiscuss\Core\Support\Actor;
		$qdiscuss_formatter = new \Qdiscuss\Core\Formatter\FormatterManager($qdiscuss_app);
		$qdiscuss_gambits = new  \Qdiscuss\Core\Search\GambitManager($qdiscuss_app);
		// \Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
		
		// Setup the Class and other Core function  binding
		$this->register_binding();
		$this->register_event_handlers();
		$this->register_post_types();
		$this->setup_permission();
		$this->register_gambits();
		$this->setup_models();
		$this->register_notification();
		$this->load_extensions();

		$qdiscuss_app['qdiscuss.formatter']->add('linkify', 'Qdiscuss\Core\Formatter\LinkifyFormatter');

		$qdiscuss_bus->mapUsing(function ($command) {
		      	return \Illuminate\Bus\Dispatcher::simpleMapping(
		      		$command, 'Qdiscuss\Core\Commands', 'Qdiscuss\Core\Handlers\Commands'
		      	);
		});

		// Add  forum asset manager
		$qdiscuss_app['qdiscuss.forum.assetManager'] = $qdiscuss_app->share(function ($app) {
			return new \Qdiscuss\Core\Support\AssetManager($app['files'], $app['path.public'].'/web', 'forum');
		});
		// Add  admin asset manager
		$qdiscuss_app['qdiscuss.admin.assetManager'] = $qdiscuss_app->share(function ($app) {
			return new  \Qdiscuss\Core\Support\AssetManager($app['files'], $app['path.public'].'/web', 'admin');
		});
	}

	public function register_binding()
	{
		global $qdiscuss_app, $qdiscuss_file;

		$qdiscuss_app->bind(
			'Illuminate\Contracts\Container\Container',
			'Illuminate\Container\Container'
		);
		$qdiscuss_app->bind(
			'Illuminate\Contracts\Bus\Dispatcher',
			'Illuminate\Bus\Dispatcher'
		);
		$qdiscuss_app->bind(
			'Illuminate\Contracts\Filesystem\Factory',
			'Illuminate\Filesystem\FilesystemManager'
		);
		$qdiscuss_app->bind(
			'League\Flysystem\FilesystemInterface',
			'League\Flysystem\Filesystem'
		);
		$qdiscuss_app->bind(
			'League\Flysystem\AdapterInterface',
			'League\Flysystem\Adapter\Local'
		);
		$qdiscuss_app->singleton('events', function(){
		  	// return new \Illuminate\Events\Dispatcher;
		  	return (new \Illuminate\Events\Dispatcher($app))->setQueueResolver(function() use ($app){
		  		return $app->make('Illuminate\Contracts\Queue\Queue');
		  	});
		});
		$qdiscuss_app->singleton('config', function(){
		  		return new \Illuminate\Config\Repository;
		});
		$qdiscuss_app->singleton('files', function(){
			return new \Illuminate\Filesystem\Filesystem;
		});
		$qdiscuss_app->singleton('path', function(){
			return __DIR__.'/../';
		});
		$qdiscuss_app->singleton('path.public', function(){
			return __DIR__.'/../public/';
		});
		$qdiscuss_app->singleton('filesystem', function() use ($qdiscuss_app) {
	  		$qdiscuss_app['config']->set("filesystems.default", "local");
	  		$qdiscuss_app['config']->set("filesystems.cloud", "s3");
	  		$qdiscuss_app['config']->set("filesystems.disks.qdiscuss-avatars", array("driver" => "local", "root" => rtrim(ABSPATH, '/') . DIRECTORY_SEPARATOR .  'wp-content' . DIRECTORY_SEPARATOR  . 'uploads'  . DIRECTORY_SEPARATOR . 'qdiscuss' . DIRECTORY_SEPARATOR . 'avatars'));
			return new \Illuminate\Filesystem\FilesystemManager($qdiscuss_app);
		});
		$qdiscuss_app->singleton('filesystem.disk', function() use ($qdiscuss_app) {
				return $qdiscuss_app['filesystem']->disk("qdiscuss-avatars")->getDriver();
		});
		$qdiscuss_app->when('Qdiscuss\Core\Handlers\Commands\UploadAvatarCommandHandler')
		 		->needs('League\Flysystem\FilesystemInterface')
		 		->give(function($app) {
			        		return $app->make('filesystem.disk');
			    		// return $app->make('Illuminate\Contracts\Filesystem\Factory')->disk('qdiscuss-avatars')->getDriver();
			    		// return $qdiscuss_file->disk('qdiscuss-avatars')->getDriver();
		    		}
		);
 		$qdiscuss_app->when('Qdiscuss\Core\Handlers\Commands\DeleteAvatarCommandHandler')
 		 		->needs('League\Flysystem\FilesystemInterface')
 		 		->give(function($app) {
 			        		return $app->make('filesystem.disk');
 			    		// return $app->make('Illuminate\Contracts\Filesystem\Factory')->disk('qdiscuss-avatars')->getDriver();
 			    		// return $qdiscuss_file->disk('qdiscuss-avatars')->getDriver();
 		    		}
 		);
		$qdiscuss_app->singleton('qdiscuss.formatter', 'Qdiscuss\Core\Formatter\FormatterManager');
		// $qdiscuss_app->singleton('qdiscuss.formatter', function () use ($qdiscuss_app) {
		// 	$formatter = new \Qdiscuss\Core\Formatter\FormatterManager($qdiscuss_app);
		// 	$formatter->add('basic', 'Qdiscuss\Core\Formatter\BasicFormatter');
		// 	return $formatter;
		// });
	              $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\DiscussionRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentDiscussionRepository'
	              );
	              $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\UserRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentUserRepository'
	              );
	              $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\PostRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentPostRepository'
	              );
	              $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\ActivityRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentActivityRepository'
	              );
	              $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
	              );
	              $qdiscuss_app->bind(
	              	'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
	              	'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
	              );
	              $qdiscuss_app->singleton('Qdiscuss\Core\Notifications\Notifier');
	              $qdiscuss_app->alias('Qdiscuss\Core\Notifications\Notifier', 'qdiscuss.notifier');
	              $qdiscuss_app->singleton('qdiscuss.forum', 'Qdiscuss\Core\Models\Forum');
	}

	public static  function register_post_types()
	{
		global $qdiscuss_app;
		\Qdiscuss\Core\Models\Post::addType('Qdiscuss\Core\Models\CommentPost');
		\Qdiscuss\Core\Models\Post::addType('Qdiscuss\Core\Models\DiscussionRenamedPost');
		\Qdiscuss\Core\Models\CommentPost::setFormatter($qdiscuss_app['qdiscuss.formatter']);
	}

      	public static  function register_gambits()
      	{
		global $qdiscuss_app, $qdiscuss_event, $qdiscuss_gambits;

		$qdiscuss_app->bind("qdiscuss.discussions.gambits", function() use($qdiscuss_app, $qdiscuss_event, $qdiscuss_gambits){
			// $qdiscuss_gambits = new  \Qdiscuss\Core\Search\GambitManager($qdiscuss_app);
			$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\AuthorGambit');
	                  	$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\UnreadGambit');
	                  	$qdiscuss_gambits->setFulltextGambit('Qdiscuss\Core\Search\Discussions\Gambits\FulltextGambit');

	                  	$qdiscuss_event->fire(new \Qdiscuss\Core\Events\RegisterDiscussionGambits($qdiscuss_gambits));

	                  	return $qdiscuss_gambits;
		});
		$qdiscuss_app->bind("qdiscuss.users.gambits", function() use($qdiscuss_app, $qdiscuss_gambits){
			// $qdiscuss_gambits = new  \Qdiscuss\Core\Search\GambitManager($qdiscuss_app);
			$qdiscuss_gambits->setFulltextGambit('Qdiscuss\Core\Search\Users\Gambits\FulltextGambit');

			$qdiscuss_event->fire(new \Qdiscuss\Core\Events\RegisterUserGambits($qdiscuss_gambits));

	                  	return $qdiscuss_gambits;
		});

      	}

	public function setup_permission()
              {
              	$this->extend(
              	    new Permission('forum.view'),
              	    new Permission('forum.startDiscussion'),
              	    new Permission('discussion.rename'),
              	    new Permission('discussion.delete'),
              	    new Permission('discussion.reply'),
              	    new Permission('post.edit'),
              	    new Permission('post.delete')
              	);

              	Forum::grantPermission(function ($grant, $user, $permission) {
              	    return $user->hasPermission('forum.'.$permission);
              	});

              	Post::grantPermission(function ($grant, $user, $permission) {
              	    return $user->hasPermission('post'.$permission);
              	});

              	// Grant view access to a post only if the user can also view the
              	// discussion which the post is in. Also, the if the post is hidden,
              	// the user must have edit permissions too.
              	Post::grantPermission('view', function ($grant) {
              	    $grant->whereCan('view', 'discussion');
              	});

              	Post::demandPermission('view', function ($demand) {
              	    $demand->whereNull('hide_user_id')
              	           ->orWhereCan('edit');
              	});

              	// Allow a user to edit their own post, unless it has been hidden by
              	// someone else.
              	Post::grantPermission('edit', function ($grant, $user) {
              	    $grant->where('user_id', $user->id)
              	          ->whereNull('hide_user_id')
              	          ->orWhere('hide_user_id', $user->id);
              	    // @todo add limitations to time etc. according to a config setting
              	});

              	User::grantPermission(function ($grant, $user, $permission) {
              	    return $user->hasPermission('user.'.$permission);
              	});

              	// Grant view access to a user if the user can view the forum.
              	User::grantPermission('view', function ($grant, $user) {
              	    $grant->whereCan('view', 'forum');
              	});

              	// Allow a user to edit their own account.
              	User::grantPermission('edit', function ($grant, $user) {
              	    $grant->where('id', $user->id);
              	});

              	Discussion::grantPermission(function ($grant, $user, $permission) {
              	    return $user->hasPermission('discussion.'.$permission);
              	});

              	// Grant view access to a discussion if the user can view the forum.
              	Discussion::grantPermission('view', function ($grant, $user) {
              	    $grant->whereCan('view', 'forum');
              	});

              	// Allow a user to rename their own discussion.
              	Discussion::grantPermission('rename', function ($grant, $user) {
              	    $grant->where('start_user_id', $user->id);
              	    // @todo add limitations to time etc. according to a config setting
              	});  
            }

            public function register_event_handlers()
            {
		global $qdiscuss_event;

		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionMetadataUpdater');
		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\UserMetadataUpdater');
		// $qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\EmailConfirmationMailer');//neychang comment
		
		// todo neychang
		// $qdiscuss_event->listen('Qdiscuss\Api\Events\SerializeAttributes', function ($event) use ($serializer, $callback) {
		// 	if ($event->serializer instanceof $serializer) {
		//               	$callback($event->attributes, $event->model);
		//             	}
		// });
            }

            public static function setup_models()
            {
            		global $qdiscuss_app;

            		\Qdiscuss\Core\Models\BaseModel::setForum($qdiscuss_app['qdiscuss.forum']);
    		//Model::setValidator($this->app['validator']);
    		// User::setHasher($this->app['hash']);
    		\Qdiscuss\Core\Models\User::setFormatter($qdiscuss_app['qdiscuss.formatter']);
    		\Qdiscuss\Core\Models\User::registerPreference('discloseOnline', 'boolval', true);
    		\Qdiscuss\Core\Models\User::registerPreference('indexProfile', 'boolval', true);
            		
            }

            public function register_notification()
            {
            		global $qdiscuss_app, $qdiscuss_event;

            		$notifier = $qdiscuss_app->make('Qdiscuss\Core\Notifications\Notifier');

            		$qdiscuss_app->singleton('qdiscuss.notifier' , function() use ($qdiscuss_app){
            			return new \Qdiscuss\Core\Notifications\Notifier($qdiscuss_app);
            		});

            		$notifier->registerMethod('alert', 'Qdiscuss\Core\Notifications\Senders\NotificationAlerter');
            		// $notifier->registerMethod('email', 'Qdiscuss\Core\Notifications\Senders\NotificationEmailer');

            		// $notifier->registerType('Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification', ['alert' => true]);

            		// $qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionRenamedNotifier');

            		// add 
            		// $this->notificationType('Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification', ['alert' => true]);
            		$this->extend(
	            		(new Qdiscuss\Extend\NotificationType('Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification'))->enableByDefault('alert')
		);
     		
	            	$qdiscuss_app->bind(
	            		'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
	            		'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
	        	);

	            	//$qdiscuss_app->alias('Qdiscuss\Core\Notifications\Notifier', 'qdisucss.notifier');
            }

            public static function load_extensions()
            {
            		$extensions = json_decode(\Illuminate\Database\Capsule\Manager::table('config')->where('key', 'extensions_enabled')->pluck('value'), true);
            		if($extensions){
            			foreach ($extensions as $extension) {
            				if (file_exists($file = extensions_path() . '/'. $extension . '/bootstrap.php')) {
            					require $file;
            				}
            			}
            		}
            }

}





