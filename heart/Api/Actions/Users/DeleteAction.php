<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\DeleteUserCommand;
use Qdiscuss\Api\Actions\DeleteAction as BaseDeleteAction;
use Qdiscuss\Api\Request;
use Illuminate\Http\Response;
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
     * Delete a user.
     *
     * @param \Qdiscuss\Api\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    protected function delete(Request $request, Response $response)
    {
        $this->bus->dispatch(
            new DeleteUserCommand($request->get('id'), $request->actor->getUser())
        );
    }
}
