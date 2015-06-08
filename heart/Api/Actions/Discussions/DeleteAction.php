<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\DeleteDiscussionCommand;
use Qdiscuss\Api\Actions\DeleteAction as BaseDeleteAction;
use Qdiscuss\Api\Request;
use Slim\Http\Response;
use Illuminate\Contracts\Bus\Dispatcher;

class DeleteAction extends BaseDeleteAction
{
	/**
	 * The command bus.
	 *
	 * @var \Illuminate\Contracts\Bus\Dispatcher
	 */
	protected $bus;

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
	 * Delete a discussion.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @param \Illuminate\Http\Response $response
	 * @return void
	 */
	protected function delete(Request $request, Response $response)
	{
		$this->bus->dispatch(
			new DeleteDiscussionCommand($request->get('id'), $request->actor->getUser())
		);
	}
}
