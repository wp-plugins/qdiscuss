<?php namespace Qdiscuss\Core\Notifications\Types;

interface EmailableNotification
{
    public function getEmailView();

    public function getEmailSubject();
}
