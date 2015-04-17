<?php namespace Qdiscuss\Api\Actions\Posts;

use Qdiscuss\Core\Commands\EditPostCommand;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\PostSerializer;

class UpdateAction extends BaseAction
{
	public function __construct()
	{
		global $qdiscuss_actor;
		$this->actor = $qdiscuss_actor;
	}
	/**
	 * Edit a post. Allows revision of content, and hiding/unhiding.
	 *
	 * @param  int $id post's id
	 * @return  json
	 */
	public function run($id)
	{
		$postId = $id;
		$params = $this->post_data();
	
		$command = new EditPostCommand($postId, $this->actor->getUser());
		$this->hydrate($command, $params->get('data'));
		$post = $this->dispatch($command, $params);

		$serializer = new PostSerializer;
		$document = $this->document()->setData($serializer->resource($post));

		echo $this->respondWithDocument($document);exit();

	}
}
