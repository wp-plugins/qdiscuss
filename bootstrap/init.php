<?php

use Illuminate\Database\Eloquent\QueueEntityResolver;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Bus\Dispatcher as Bus;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use League\Flysystem\Adapter\Local;
use Qdiscuss\Core\Models\CommentPost;
use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Models\BaseModel as Model;
use Qdiscuss\Core\Models\Forum;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Core\Support\AssetManager;
use Qdiscuss\Core\Search\GambitManager;
use Qdiscuss\Core\Formatter\FormatterManager;
use Qdiscuss\Core\Events\RegisterDiscussionGambits;
use Qdiscuss\Core\Events\RegisterUserGambits;
use Qdiscuss\Extend\NotificationType;
use Qdiscuss\Extend\ActivityType;
use Qdiscuss\Extend\Permission;
use Qdiscuss\App;
use Qdiscuss\Core;

class Qdiscuss extends App {
	
	public  function init()
	{
		global $qdiscuss_app, $qdiscuss_bus, $qdiscuss_event, $qdiscuss_gambits, $qdiscuss_actor, $qdiscuss_formatter, $qdiscuss_title, $qdiscuss_desc, $qdiscuss_endpoint;

		$qdiscuss_app = new Container;
		$qdiscuss_bus = new Bus($qdiscuss_app);
		$qdiscuss_event = new Dispatcher;
		$qdiscuss_actor = new Actor;
		$qdiscuss_formatter = new FormatterManager($qdiscuss_app);
		$qdiscuss_gambits = new  GambitManager($qdiscuss_app);
		
		$qdiscuss_app->singleton('config', function(){
		  	return new \Illuminate\Config\Repository;
		});
		// Setup the Class and other Core function  binding
		$this->register_database();
		$this->register_binding();
		$this->register_event_handlers();
		$this->register_post_types();
		$this->setup_permission();
		$this->register_gambits();
		$this->setup_models();
		$this->load_extensions();
		
		$qdiscuss_app['qdiscuss.formatter']->add('linkify', 'Qdiscuss\Core\Formatter\LinkifyFormatter');

		$qdiscuss_bus->mapUsing(function ($command) {
		      	return Bus::simpleMapping(
		      		$command, 'Qdiscuss\Core\Commands', 'Qdiscuss\Core\Handlers\Commands'
		      	);
		});

		// Add  forum asset manager
		$qdiscuss_app['qdiscuss.forum.assetManager'] = $qdiscuss_app->share(function ($app) {
			return new AssetManager($app['files'], $app['path.public'].'/web', 'forum');
		});
		// Add  admin asset manager
		$qdiscuss_app['qdiscuss.admin.assetManager'] = $qdiscuss_app->share(function ($app) {
			return new  AssetManager($app['files'], $app['path.public'].'/web', 'admin');
		});
	}

	public function register_binding()
	{
		global $qdiscuss_app, $qdiscuss_file;

		$qdiscuss_app->singleton('path', function(){
			return __DIR__.'/../';
		});
		$qdiscuss_app->singleton('path.public', function(){
			return __DIR__.'/../public/';
		});
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
		  	return (new \Illuminate\Events\Dispatcher($app))->setQueueResolver(function() use ($app){
		  		return $app->make('Illuminate\Contracts\Queue\Queue');
		  	});
		});
		$qdiscuss_app->singleton('files', function(){
			return new \Illuminate\Filesystem\Filesystem;
		});
		$qdiscuss_app->singleton('filesystem', function() use ($qdiscuss_app) {
	  		$qdiscuss_app['config']->set("filesystems.default", "local");
	  		$qdiscuss_app['config']->set("filesystems.cloud", "s3");
	  		$qdiscuss_app['config']->set("filesystems.disks.qdiscuss-avatars", array("driver" => "local", "root" => qd_upload_path() . DIRECTORY_SEPARATOR . 'avatars'));
			return new \Illuminate\Filesystem\FilesystemManager($qdiscuss_app);
		});
		$qdiscuss_app->singleton('filesystem.disk', function() use ($qdiscuss_app) {
				return $qdiscuss_app['filesystem']->disk("qdiscuss-avatars")->getDriver();
		});
		$qdiscuss_app->when('Qdiscuss\Core\Handlers\Commands\UploadAvatarCommandHandler')
		 		->needs('League\Flysystem\FilesystemInterface')
		 		->give(function($app) {
			        		return $app->make('filesystem.disk');
		    		}
		);
 		$qdiscuss_app->when('Qdiscuss\Core\Handlers\Commands\DeleteAvatarCommandHandler')
 		 		->needs('League\Flysystem\FilesystemInterface')
 		 		->give(function($app) {
 			        		return $app->make('filesystem.disk');
 		    		}
 		);
		$qdiscuss_app->singleton('qdiscuss.formatter', 'Qdiscuss\Core\Formatter\FormatterManager');
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
	             // not work @todo
	             $qdiscuss_app->bind(
	                            'Qdiscuss\Core\Repositories\ActivityRepositoryInterface',
	                            'Qdiscuss\Core\Repositories\EloquentActivityRepository'
	             );
	             $qdiscuss_app->bind(
	                         'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
	                         'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
	             );
	             $qdiscuss_app->singleton('qdiscuss.forum', 'Qdiscuss\Core\Models\Forum');
	}

	public static function register_database()
	{
		global $qdiscuss_app, $qdiscuss_config;
		$qdiscuss_app['config']->set("database.connections", $qdiscuss_config['database']);
		// $capsule = new Capsule;
		// $capsule->addConnection($qdiscuss_config['database']);
		// $capsule->setEventDispatcher(new Dispatcher(new Container));
		// $capsule->setAsGlobal();
		// $capsule->bootEloquent();
		$qdiscuss_app->singleton('Illuminate\Contracts\Queue\EntityResolver', function()
		{
			return new QueueEntityResolver;
		});
		$qdiscuss_app->singleton('db.factory', function($app)
		{
			return new ConnectionFactory($app);
		});
		$qdiscuss_app->singleton('db', function($app)
		{
			return new DatabaseManager($app, $app['db.factory']);
		});
	}

	public static  function register_post_types()
	{
		global $qdiscuss_app;
		Post::addType('Qdiscuss\Core\Models\CommentPost');
		Post::addType('Qdiscuss\Core\Models\DiscussionRenamedPost');
		CommentPost::setFormatter($qdiscuss_app['qdiscuss.formatter']);
	}

      	public static  function register_gambits()
      	{
		global $qdiscuss_app, $qdiscuss_event, $qdiscuss_gambits;

		$qdiscuss_app->bind("qdiscuss.discussions.gambits", function() use($qdiscuss_app, $qdiscuss_event, $qdiscuss_gambits){
			$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\AuthorGambit');
	                  	$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\UnreadGambit');
	                  	$qdiscuss_gambits->setFulltextGambit('Qdiscuss\Core\Search\Discussions\Gambits\FulltextGambit');

	                  	$qdiscuss_event->fire(new \Qdiscuss\Core\Events\RegisterDiscussionGambits($qdiscuss_gambits));

	                  	return $qdiscuss_gambits;
		});
		$qdiscuss_app->bind("qdiscuss.users.gambits", function() use($qdiscuss_app, $qdiscuss_gambits){
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
              	          ->where(function ($query) use ($user) {
              	          		$query->whereNull('hide_user_id')
              	          	             ->orWhere('hide_user_id', $user->id);
             		             });
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
		
		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionRenamedNotifier');
		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\UserActivitySyncer');

		$this->extend(
		            (new NotificationType('Qdiscuss\Core\Notifications\DiscussionRenamedNotification', 'Qdiscuss\Api\Serializers\DiscussionBasicSerializer'))
		                ->enableByDefault('alert'),
		             (new ActivityType('Qdiscuss\Core\Activity\PostedActivity', 'Qdiscuss\Api\Serializers\PostBasicSerializer')),
		             (new ActivityType('Qdiscuss\Core\Activity\StartedDiscussionActivity', 'Qdiscuss\Api\Serializers\PostBasicSerializer')),
		             (new ActivityType('Qdiscuss\Core\Activity\JoinedActivity', 'Qdiscuss\Api\Serializers\UserBasicSerializer'))
		);

            }

            public static function setup_models()
            {
            		global $qdiscuss_app;

            		Model::setForum($qdiscuss_app['qdiscuss.forum']);
    		//Model::setValidator($this->app['validator']);
    		// User::setHasher($this->app['hash']);
    		User::setFormatter($qdiscuss_app['qdiscuss.formatter']);
    		User::registerPreference('discloseOnline', 'boolval', true);
    		User::registerPreference('indexProfile', 'boolval', true);
            		
            }

            public static function load_extensions()
            {
            		$extensions = json_decode(Core::config('extensions_enabled'), true);

            		if($extensions){
            			foreach ($extensions as $extension) {
            				if (file_exists($file = qd_extensions_path() . '/'. $extension . '/bootstrap.php')) {
            					require_once($file);
            				}
            			}
            		}
            }

}





