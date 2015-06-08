<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\StartDiscussionCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
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
	 * The default forum instance.
	 *
	 * @var \Qdiscuss\Core\Models\Forum
	 */
	protected $forum;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\DiscussionSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		'posts' => true,
		'startUser' => true,
		'lastUser' => true,
		'startPost' => true,
		'lastPost' => true
	];

	/**
	 * Instantiate the action.
	 *
	 * @param \Illuminate\Contracts\Bus\Dispatcher $bus
	 * @param \Qdiscuss\Core\Models\Forum $forum
	 */
	public function __construct(Dispatcher $bus, Forum $forum)
	{
		$this->bus = $bus;
		$this->forum = $forum;
	}

	/**
	 * Create a discussion according to input from the API request.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Discussion
	 */
	protected function create(JsonApiRequest $request, JsonApiResponse $response)
	{

		$user = $request->actor->getUser();

		$discussion = $this->bus->dispatch(

			new StartDiscussionCommand($user, $this->forum, $request->get('data'))
		);

		// After creating the discussion, we assume that the user has seen all
		// of the posts in the discussion; thus, we will mark the discussion
		// as read if they are logged in.
		if ($user->exists) {
			$this->bus->dispatch(
				new ReadDiscussionCommand($discussion->id, $user, 1)
			);
		}

		return $discussion;
	}
}
