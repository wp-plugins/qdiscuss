<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Qdiscuss\Api\Actions\SerializeResourceAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class ShowAction extends SerializeResourceAction
{
	/**
	 * @var \Qdiscuss\Core\Repositories\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\UserSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		'groups' => true
	];

	/**
	 * Instantiate the action.
	 *
	 * @param \Qdiscuss\Core\Repositories\UserRepositoryInterface $users
	 */
	public function __construct(UserRepositoryInterface $users)
	{
		$this->users = $users;
	}

	/**
	 * Get a single user, ready to be serialized and assigned to the JsonApi
	 * response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Discussion
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		$id = $request->get('id');

		if (! is_numeric($id)) {
			$id = $this->users->getIdForUsername($id);
		}

		return $this->users->findOrFail($id, $request->actor->getUser());
	}
}
