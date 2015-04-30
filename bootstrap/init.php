<?php
class Qdiscuss {
	
	public  static function init()
	{
		global $qdiscuss_app, $qdiscuss_bus, $qdiscuss_event, $qdiscuss_params, $qdiscuss_actor, $qdiscuss_formatter, $qdiscuss_title, $qdiscuss_desc, $qdiscuss_endpoint;

		$qdiscuss_app = new \Illuminate\Container\Container;
		$qdiscuss_bus = new \Illuminate\Bus\Dispatcher($qdiscuss_app);
		$qdiscuss_event = new \Illuminate\Events\Dispatcher;
		$qdiscuss_params = new \Qdiscuss\Core\Actions\ApiParams($_GET);
		$qdiscuss_actor = new \Qdiscuss\Core\Support\Actor;
		$qdiscuss_formatter = new \Qdiscuss\Core\Formatter\FormatterManager($qdiscuss_app);

		\Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
		
		// Setup the Class and other Core function  binding
		self::register_binding();
		self::register_event_handlers();
		self::register_post_types();
		self::setup_permission();
		self::register_gambits();
		self::setup_models();
		self::register_notification();

		$qdiscuss_bus->mapUsing(function ($command) {
		      	return \Illuminate\Bus\Dispatcher::simpleMapping(
		      		$command, 'Qdiscuss\Core\Commands', 'Qdiscuss\Core\Handlers\Commands'
		      	);
		});
	}

	public  static function register_binding()
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
		  $qdiscuss_app->singleton('config', function(){
		  		return new \Illuminate\Config\Repository;
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
		$qdiscuss_app->singleton('qdiscuss.extensions', 'Qdiscuss\Core\Support\Extensions\Manager');
		$qdiscuss_app->bind('qdiscuss.discussionFinder', 'Qdiscuss\Core\Discussions\DiscussionFinder');

		$qdiscuss_app->singleton('qdiscuss.formatter', function () use ($qdiscuss_app) {
			// global $qdiscuss_formatter;
			$qdiscuss_formatter = new \Qdiscuss\Core\Formatter\FormatterManager($qdiscuss_app);
			$qdiscuss_formatter->add('basic', 'Qdiscuss\Core\Formatter\BasicFormatter');
		    	return $qdiscuss_formatter;
		});
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
	              $qdiscuss_app->singleton('qdiscuss.forum', 'Qdiscuss\Core\Models\Forum');
	}

	public static  function register_post_types()
	{
		global $qdiscuss_app;
		\Qdiscuss\Core\Models\Post::addType('comment', 'Qdiscuss\Core\Models\CommentPost');
		\Qdiscuss\Core\Models\Post::addType('discussionRenamed', 'Qdiscuss\Core\Models\DiscussionRenamedPost');
		\Qdiscuss\Core\Models\CommentPost::setFormatter($qdiscuss_app['qdiscuss.formatter']);
	}

      	public static  function register_gambits()
      	{
		global $qdiscuss_app;

		$qdiscuss_app->bind("qdiscuss.discussions.gambits", function() use($qdiscuss_app){
			$qdiscuss_gambits = new  \Qdiscuss\Core\Search\GambitManager($qdiscuss_app);
			$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\AuthorGambit');
	                  	$qdiscuss_gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\UnreadGambit');
	                  	$qdiscuss_gambits->setFulltextGambit('Qdiscuss\Core\Search\Discussions\Gambits\FulltextGambit');
	                  	return $qdiscuss_gambits;
		});
		$qdiscuss_app->bind("qdiscuss.users.gambits", function() use($qdiscuss_app){
			$qdiscuss_gambits = new  \Qdiscuss\Core\Search\GambitManager($qdiscuss_app);
			$qdiscuss_gambits->setFulltextGambit('Qdiscuss\Core\Search\Users\Gambits\FulltextGambit');
	                  	return $qdiscuss_gambits;
		});

      	}

	public  static function setup_permission()
              {
            		\Qdiscuss\Core\Models\Forum::grantPermission(function ($grant, $user, $permission) {
            			return $user->hasPermission($permission, 'forum');
            		});

            		\Qdiscuss\Core\Models\Post::grantPermission(function ($grant, $user, $permission) {
            		    return $user->hasPermission($permission, 'post');
            		});

            		// Grant view access to a post only if the user can also view the
            		// discussion which the post is in. Also, the if the post is hidden,
            		// the user must have edit permissions too.
            		\Qdiscuss\Core\Models\Post::grantPermission('view', function ($grant) {
            		    $grant->whereCan('view', 'discussion');
            		});

            		\Qdiscuss\Core\Models\Post::demandPermission('view', function ($demand) {
            		    $demand->whereNull('hide_user_id')
            		           ->orWhereCan('edit');
            		});

            		// Allow a user to edit their own post, unless it has been hidden by
            		// someone else.
            		\Qdiscuss\Core\Models\Post::grantPermission('edit', function ($grant, $user) {
            		    $grant->whereCan('editOwn')
            		          ->where('user_id', $user->id);
            		});

            		\Qdiscuss\Core\Models\Post::demandPermission('editOwn', function ($demand, $user) {
            		    $demand->whereNull('hide_user_id');
            		    if ($user) {
            		        $demand->orWhere('hide_user_id', $user->id);
            		    }
            		});

            		\Qdiscuss\Core\Models\User::grantPermission(function ($grant, $user, $permission) {
            		    return $user->hasPermission($permission, 'forum');
            		});

            		// Grant view access to a user if the user can view the forum.
            		\Qdiscuss\Core\Models\User::grantPermission('view', function ($grant, $user) {
            		    $grant->whereCan('view', 'forum');
            		});

            		// Allow a user to edit their own account.
            		\Qdiscuss\Core\Models\User::grantPermission('edit', function ($grant, $user) {
            		    $grant->where('id', $user->id);
            		});

            		\Qdiscuss\Core\Models\Discussion::grantPermission(function ($grant, $user, $permission) {
            		    return $user->hasPermission($permission, 'discussion');
            		});

            		// Grant view access to a discussion if the user can view the forum.
            		\Qdiscuss\Core\Models\Discussion::grantPermission('view', function ($grant, $user) {
            		    $grant->whereCan('view', 'forum');
            		});

            		// Allow a user to edit their own discussion.
            		\Qdiscuss\Core\Models\Discussion::grantPermission('edit', function ($grant, $user) {
            		    if ($user->hasPermission('editOwn', 'discussion')) {
            		        $grant->where('start_user_id', $user->id);
            		    }
            		});            
            }

            public  static function register_event_handlers()
            {
		global $qdiscuss_event;

		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionMetadataUpdater');
		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\UserMetadataUpdater');
		// $qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\EmailConfirmationMailer');//neychang comment
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

            public static function register_notification()
            {
            		global $qdiscuss_app, $qdiscuss_event;

            		$notifier = $qdiscuss_app->make('Qdiscuss\Core\Notifications\Notifier');

            		$notifier->registerMethod('alert', 'Qdiscuss\Core\Notifications\Senders\NotificationAlerter');
            		// $notifier->registerMethod('email', 'Flarum\Core\Notifications\Senders\NotificationEmailer');

            		$notifier->registerType('Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification', ['alert' => true]);

            		$qdiscuss_event->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionRenamedNotifier');
            }

}





