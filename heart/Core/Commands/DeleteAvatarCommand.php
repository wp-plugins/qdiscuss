<?php namespace Qdiscuss\Core\Commands;

use RuntimeException;

class DeleteAvatarCommand
{
	public $userId;
	public $actor;
	public function __construct($userId, $actor)
	{
		if (empty($userId) || !intval($userId)) {
			throw new RuntimeException('No valid user ID specified.');
		}
		global $qdiscuss_actor;
		$this->userId = $userId;
		$this->actor = $qdiscuss_actor->getUser();
	}
}