<?php namespace Qdiscuss\Api\Actions\Posts;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Qdiscuss\Core\Repositories\PostRepositoryInterface;
use Qdiscuss\Api\Actions\SerializeResourceAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class ShowAction extends SerializeResourceAction
{
	/**
	 * @var \Qdiscuss\Core\Repositories\PostRepositoryInterface
	 */
	protected $posts;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\PostSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		'user' => true,
		'user.groups' => true,
		'editUser' => true,
		'hideUser' => true,
		'discussion' => false
	];

	/**
	 * Instantiate the action.
	 *
	 * @param \Qdiscuss\Core\Repositories\PostRepositoryInterface $posts
	 */
	public function __construct(PostRepositoryInterface $posts)
	{
		$this->posts = $posts;
	}

	/**
	 * Get a single post, ready to be serialized and assigned to the JsonApi
	 * response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Discussion
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		return $this->posts->findOrFail($request->get('id'), $request->actor->getUser());
	}
}
