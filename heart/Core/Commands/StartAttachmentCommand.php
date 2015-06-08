<?php namespace Qdiscuss\Core\Commands;

use Qdiscuss\Core\Support\FileUpload;

class StartAttachmentCommand
{
	public $userId;

	public $file;

	public $data;

	public function __construct($userId, $file, $actor)
	{

		if (empty($userId) || !intval($userId)) {
		    throw new RuntimeException('No valid user ID specified.');
		}

		if (is_null($file)) {
		    throw new RuntimeException('No file to upload');
		}

		$this->userId = $userId;
		$this->file = new FileUpload($file);
		$this->actor = $actor;
	}
}
