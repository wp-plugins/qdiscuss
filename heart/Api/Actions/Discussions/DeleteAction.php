<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\DeleteDiscussionCommand;
use Qdiscuss\Core\Actions\BaseAction;

class DeleteAction extends BaseAction
{
	public function __construct()
	{
		global $qdiscuss_actor;
		$this->actor = $qdiscuss_actor;
	}

	/**
	 * Delete a discussion.
	 *
	 * @param  int $id discussion's id
	 * @return  Response
	 */
	public function run($id)
	{
		$discussionId = $id;

		$command = new DeleteDiscussionCommand($discussionId, $this->actor->getUser());
		$this->dispatch($command, $params);

		echo $this->respondWithoutContent();exit();

	}
}
