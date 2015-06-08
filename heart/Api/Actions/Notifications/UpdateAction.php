<?php namespace Qdiscuss\Api\Actions\Notifications;

use Qdiscuss\Core\Commands\ReadNotificationCommand;
use Qdiscuss\Api\Actions\SerializeResourceAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateAction extends SerializeResourceAction
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
    public static $serializer = 'Qdiscuss\Api\Serializers\NotificationSerializer';

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
     * Mark a notification as read, and return it ready to be serialized and
     * assigned to the JsonApi response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Qdiscuss\Core\Models\Notification
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        return $this->bus->dispatch(
            new ReadNotificationCommand($request->get('id'), $request->actor->getUser())
        );
    }
}
