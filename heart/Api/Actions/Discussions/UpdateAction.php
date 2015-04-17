<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\EditDiscussionCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Api\Serializers\DiscussionSerializer;

class UpdateAction extends BaseAction
{
	public function __construct()
	{
		
	}

	/**
	* Edit a discussion. Allows renaming the discussion, and updating its read
	* state with regards to the current user.
	*
	* @param  integer $id
	* @return Response
	*/
	public  function run($id)
	{
		global $qdiscuss_params, $qdiscuss_actor;
		$user = $qdiscuss_actor->getUser();
		$params = $this->post_data();
		
		if ($data = array_except($params->get('data'), ['readNumber'])) {
		   	try {
				$command = new EditDiscussionCommand($id, $user);
				$this->hydrate($command, $params->get('data'));
				$discussion = $this->dispatch($command, $params);
		    	} catch (PermissionDeniedException $e) {
				// Temporary fix. See @todo below
				$discussion = \Qdiscuss\Core\Models\Discussion::find($id);
			}
		}

		if ($readNumber = $params->get('data.readNumber')) {
		    $command = new ReadDiscussionCommand($id, $user, $readNumber);
		    $this->dispatch($command, $params);
		}

		$serializer = new DiscussionSerializer(['addedPosts', 'addedPosts.user']);
		$document = $this->document()->setData($serializer->resource($discussion));

		echo $this->respondWithDocument($document);exit();
	}
}
