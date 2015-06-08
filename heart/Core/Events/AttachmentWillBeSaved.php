<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Models\Attachment;

class AttachmentWillBeSaved
{
    public $attachment;

    public $command;

    public function __construct(Attachment $attachment, $command)
    {
        $this->attachment = $attachment;
        $this->command = $command;
    }
}

