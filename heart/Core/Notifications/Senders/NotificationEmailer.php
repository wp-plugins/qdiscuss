<?php namespace Qdiscuss\Core\Notifications\Senders;

use Qdiscuss\Core\Notifications\Types\Notification;
use Qdiscuss\Core\Models\Forum;
// use Illuminate\Mail\Mailer;neychang comment
use ReflectionClass;

class NotificationEmailer implements NotificationSender
{
    public function __construct($mailer, Forum $forum)
    {
        $this->mailer = $mailer;
        $this->forum = $forum;
    }

    public function send(Notification $notification)
    {
        // neychang comment
        // $this->mailer->send($notification->getEmailView(), ['notification' => $notification], function ($message) use ($notification) {
        //     $recipient = $notification->getRecipient();
        //     $message->to($recipient->email, $recipient->username)
        //             ->subject('['.$this->forum->title.'] '.$notification->getEmailSubject());
        // });
    }

    public function compatibleWith($class)
    {
        return (new ReflectionClass($class))->implementsInterface('Qdiscuss\Core\Notifications\Types\EmailableNotification');
    }
}
