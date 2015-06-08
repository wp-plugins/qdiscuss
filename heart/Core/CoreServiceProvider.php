<?php namespace Qdiscuss\Core;

use Illuminate\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Qdiscuss\Support\ServiceProvider;
use Qdiscuss\Core\Formatter\FormatterManager;
use Qdiscuss\Core\Models\CommentPost;
use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Models\BaseModel as Model;
use Qdiscuss\Core\Models\Forum;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Search\GambitManager;
use League\Flysystem\Adapter\Local;
use Qdiscuss\Core\Events\RegisterDiscussionGambits;
use Qdiscuss\Core\Events\RegisterUserGambits;
use Qdiscuss\Extend\Permission;
use Qdiscuss\Extend\ActivityType;
use Qdiscuss\Extend\NotificationType;

class CoreServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//$this->loadViewsFrom(__DIR__.'/../../views', 'qdiscuss');

		$this->registerEventHandlers($this->app['events']);
		$this->registerPostTypes();
		$this->registerPermissions();
		$this->registerGambits();
		$this->setupModels();

		$this->app['qdiscuss.formatter']->add('linkify', 'Qdiscuss\Core\Formatter\LinkifyFormatter');

		$this->app['bus']->mapUsing(function ($command) {
			return Bus::simpleMapping(
				$command,
				'Qdiscuss\Core\Commands',
				'Qdiscuss\Core\Handlers\Commands'
			);
		});

		$this->app['events']->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionRenamedNotifier');
		$this->app['events']->subscribe('Qdiscuss\Core\Handlers\Events\UserActivitySyncer');

		$this->extend(
			(new NotificationType('Qdiscuss\Core\Notifications\DiscussionRenamedNotification', 'Qdiscuss\Api\Serializers\DiscussionBasicSerializer'))
				->enableByDefault('alert'),
			(new ActivityType('Qdiscuss\Core\Activity\PostedActivity', 'Qdiscuss\Api\Serializers\PostBasicSerializer')),
			(new ActivityType('Qdiscuss\Core\Activity\StartedDiscussionActivity', 'Qdiscuss\Api\Serializers\PostBasicSerializer')),
			(new ActivityType('Qdiscuss\Core\Activity\JoinedActivity', 'Qdiscuss\Api\Serializers\UserBasicSerializer'))
		);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register a singleton entity that represents this forum. This entity
		// will be used to check for global forum permissions (like viewing the
		// forum, registering, and starting discussions.)
		$this->app->singleton('qdiscuss.forum', 'Qdiscuss\Core\Models\Forum');

		$this->app->singleton('qdiscuss.formatter', 'Qdiscuss\Core\Formatter\FormatterManager');

		$this->app->bind(
			'Qdiscuss\Core\Repositories\DiscussionRepositoryInterface',
			'Qdiscuss\Core\Repositories\EloquentDiscussionRepository'
		);
		$this->app->bind(
			'Qdiscuss\Core\Repositories\PostRepositoryInterface',
			'Qdiscuss\Core\Repositories\EloquentPostRepository'
		);
		$this->app->bind(
			'Qdiscuss\Core\Repositories\UserRepositoryInterface',
			'Qdiscuss\Core\Repositories\EloquentUserRepository'
		);
		$this->app->bind(
			'Qdiscuss\Core\Repositories\ActivityRepositoryInterface',
			'Qdiscuss\Core\Repositories\EloquentActivityRepository'
		);
		// @todo
		// $avatarFilesystem = function (Container $app) {
		// 	return $app->make('Illuminate\Contracts\Filesystem\Factory')->disk('qdiscuss-avatars')->getDriver();
		// };

		// $this->app->when('Qdiscuss\Core\Handlers\Commands\UploadAvatarCommandHandler')
		// 	->needs('League\Flysystem\FilesystemInterface')
		// 	->give($avatarFilesystem);

		// $this->app->when('Qdiscuss\Core\Handlers\Commands\DeleteAvatarCommandHandler')
		// 	->needs('League\Flysystem\FilesystemInterface')
		// 	->give($avatarFilesystem);
		// 	
		$this->app->when('Qdiscuss\Core\Handlers\Commands\UploadAvatarCommandHandler')
		 	->needs('League\Flysystem\FilesystemInterface')
		 	->give(function($app) {
			       	return $app->make('filesystem.disk.avatars');
		});
		$this->app->when('Qdiscuss\Core\Handlers\Commands\DeleteAvatarCommandHandler')
			->needs('League\Flysystem\FilesystemInterface')
			->give(function($app) {
			    	return $app->make('filesystem.disk.avatars');
		});
		$this->app->when('Qdiscuss\Core\Handlers\Commands\StartAttachmentCommandHandler')
		 	->needs('League\Flysystem\FilesystemInterface')
		 	->give(function($app) {
			       	return $app->make('filesystem.disk.attachments');
		});
		$this->app->bind(
			'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
			'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
		);
	}

	public function registerGambits()
	{
		$this->app->when('Qdiscuss\Core\Search\Discussions\DiscussionSearcher')
			->needs('Qdiscuss\Core\Search\GambitManager')
			->give(function () {
				$gambits = new GambitManager($this->app);
				$gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\AuthorGambit');
				$gambits->add('Qdiscuss\Core\Search\Discussions\Gambits\UnreadGambit');
				$gambits->setFulltextGambit('Qdiscuss\Core\Search\Discussions\Gambits\FulltextGambit');

				event(new RegisterDiscussionGambits($gambits));

				return $gambits;
			});

		$this->app->when('Qdiscuss\Core\Search\Users\UserSearcher')
			->needs('Qdiscuss\Core\Search\GambitManager')
			->give(function () {
				$gambits = new GambitManager($this->app);
				$gambits->setFulltextGambit('Qdiscuss\Core\Search\Users\Gambits\FulltextGambit');

				event(new RegisterUserGambits($gambits));

				return $gambits;
			});
	}

	public function registerPostTypes()
	{
		Post::addType('Qdiscuss\Core\Models\CommentPost');
		Post::addType('Qdiscuss\Core\Models\DiscussionRenamedPost');

		CommentPost::setFormatter($this->app['qdiscuss.formatter']);
	}

	public function registerEventHandlers()
	{
		$this->app['events']->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionMetadataUpdater');
		$this->app['events']->subscribe('Qdiscuss\Core\Handlers\Events\UserMetadataUpdater');
		// $this->app['events']->subscribe('Qdiscuss\Core\Handlers\Events\EmailConfirmationMailer');@todo
	}

	public function setupModels()
	{
		Model::setForum($this->app['qdiscuss.forum']);
		// Model::setValidator($this->app['validator']);@todo

		// User::setHasher($this->app['hash']);@todo
		User::setFormatter($this->app['qdiscuss.formatter']);

		User::registerPreference('discloseOnline', 'boolval', true);
		User::registerPreference('indexProfile', 'boolval', true);
	}

	public function registerPermissions()
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
}
