<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Models\Forum;
use Qdiscuss\Core\Commands\RegisterUserCommand;
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
    public static $serializer = 'Qdiscuss\Api\Serializers\UserSerializer';

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
     * Register a user according to input from the API request.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Qdiscuss\Core\Models\User
     */
    protected function create(JsonApiRequest $request, JsonApiResponse $response)
    {
        return $this->bus->dispatch(
            new RegisterUserCommand($request->actor->getUser(), $this->forum, $request->get('data'))
        );
    }
}
