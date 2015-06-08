<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\UploadAvatarCommand;
use Qdiscuss\Api\Actions\SerializeResourceAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;
use Illuminate\Contracts\Bus\Dispatcher;

class UploadAvatarAction extends SerializeResourceAction
{
	/**
	 * @var \Illuminate\Contracts\Bus\Dispatcher
	 */
	protected $bus;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\UserSerializer';

	/**
	 * Instantiate the action.
	 *
	 * @param \Illuminate\Contracts\Bus\Dispatcher $bus
	 */
	public function __construct(Dispatcher $bus)
	{
		$this->bus = $bus;
	}

	/**
	 * Upload an avatar for a user, and return the user ready to be serialized
	 * and assigned to the JsonApi response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\User
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		return $this->bus->dispatch(
			new UploadAvatarCommand($request->get('id'), $request->get('avatar'), $request->actor->getUser())
		);
	}
}
