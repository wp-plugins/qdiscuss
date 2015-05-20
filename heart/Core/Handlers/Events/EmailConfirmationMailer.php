<?php namespace Qdiscuss\Core\Handlers\Events;

// use Illuminate\Mail\Mailer;// neychang comment
use Qdiscuss\Core\Events\UserWasRegistered;
use Qdiscuss\Core\Events\EmailWasChanged;
use Config;
use Illuminate\Contracts\Events\Dispatcher;

class EmailConfirmationMailer
{
    protected $mailer;

    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Contracts\Events\Dispatcher;
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('Qdiscuss\Events\UserWasRegistered', __CLASS__.'@whenUserWasRegistered');
        $events->listen('Qdiscuss\Events\EmailWasChanged', __CLASS__.'@whenEmailWasChanged');
    }

    public function whenUserWasRegistered(UserWasRegistered $event)
    {
        $user = $event->user;

        $forumTitle = Config::get('flarum::forum_title');

        $data = [
            'username' => $user->username,
            'forumTitle' => $forumTitle,
            'url' => route('flarum.confirm', ['id' => $user->id, 'token' => $user->confirmation_token])
        ];

        // neychang
        // $this->mailer->send(['text' => 'flarum::emails.confirm'], $data, function ($message) use ($user, $forumTitle) {
        //     $message->to($user->email);
        //     $message->subject('['.$forumTitle.'] Email Address Confirmation');
        // });
    }

    public function whenEmailWasChanged(EmailWasChanged $event)
    {

    }
}
