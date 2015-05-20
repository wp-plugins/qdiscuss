<?php namespace Qdiscuss\Core\Notifications\Types;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Discussion;
use Qdiscuss\Core\Models\DiscussionRenamedPost;

class DiscussionRenamedNotification extends Notification implements AlertableNotification
{
	protected $discussion;

	protected $sender;

	protected $post;

	public function __construct(Discussion $discussion, User $sender, DiscussionRenamedPost $post = null)
	{
		$this->discussion = $discussion;
		$this->sender = $sender;
		$this->post = $post;
	}

	public function getSubject()
	{
		return $this->discussion;
	}

	public function getSender()
	{
		return $this->sender;
	}

	public function getAlertData()
	{
		return ['postNumber' => $this->post->number];
	}

	public static function getType()
	{
		return 'discussionRenamed';
	}

	public static function getSubjectModel()
	{
		return 'Qdiscuss\Core\Models\Discussion';
	}
}