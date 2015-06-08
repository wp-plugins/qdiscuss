<?php namespace Qdiscuss\Api\Actions\Attachments;

use Qdiscuss\Core\Commands\StartAttachmentCommand;
// use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Models\Forum;
use Qdiscuss\Api\Actions\CreateAction as BaseCreateAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;
use Illuminate\Contracts\Bus\Dispatcher;

class CreateAction extends BaseCreateAction
{
	/**
	 * The command bus.
	 *
	 * @var \Illuminate\Contracts\Bus\Dispatcher
	 */
	protected $bus;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\AttachmentSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		
	];

	/**
	 * Instantiate the action.
	 *
	 * @param \Illuminate\Contracts\Bus\Dispatcher $bus
	 * @param \Qdiscuss\Core\Models\Forum $forum
	 */
	public function __construct(Dispatcher $bus)
	{
		$this->bus = $bus;
	}

	/**
	 * Create a attachment according to input from the API request.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Discussion
	 */
	protected function create(JsonApiRequest $request, JsonApiResponse $response)
	{
		$user = $request->actor->getUser();

		$attachment = $this->bus->dispatch(
			new StartAttachmentCommand($user->id, $request->get('data'), $user)
		);

		return $attachment;
	}
}
