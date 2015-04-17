<?php namespace Qdiscuss\Core\Notifications;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Qdiscuss\Core\Models\User;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $notifier = app('Qdiscuss\Core\Notifications\Notifier');

        $notifier->registerMethod('alert', 'Qdiscuss\Core\Notifications\Senders\NotificationAlerter');
        $notifier->registerMethod('email', 'Qdiscuss\Core\Notifications\Senders\NotificationEmailer');

        $notifier->registerType('Qdiscuss\Core\Notifications\Types\DiscussionRenamedNotification', ['alert' => true]);

        $events->subscribe('Qdiscuss\Core\Handlers\Events\DiscussionRenamedNotifier');
    }

    public function register()
    {
        $this->app->bind(
            'Qdiscuss\Core\Repositories\NotificationRepositoryInterface',
            'Qdiscuss\Core\Repositories\EloquentNotificationRepository'
        );

        $this->app->singleton('Qdiscuss\Core\Notifications\Notifier');
    }
}
