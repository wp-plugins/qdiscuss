<?php namespace Qdiscuss\Api\Actions\Notifications;

// use Qdiscuss\Core\Repositories\NotificationRepositoryInterface;
use Qdiscuss\Core\Repositories\EloquentNotificationRepository as NotificationRepositoryInterface;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
    /**
     * @var \Qdiscuss\Core\Repositories\NotificationRepositoryInterface
     */
    protected $notifications;

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public static $serializer = 'Qdiscuss\Api\Serializers\NotificationSerializer';

    /**
     * The relations that are included by default.
     *
     * @var array
     */
    public static $include = [
        'sender' => true,
        'subject' => true,
        'subject.discussion' => true
    ];

    /**
     * The maximum number of records that can be requested.
     *
     * @var integer
     */
    public static $limitMax = 50;

    /**
     * The number of records included by default.
     *
     * @var integer
     */
    public static $limit = 10;

    /**
     * Instantiate the action.
     *
     * @param \Qdiscuss\Core\Repositories\NotificationRepositoryInterface $notifications
     */
    public function __construct(NotificationRepositoryInterface $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * Get the notification results, ready to be serialized and assigned to the
     * document response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        if (! $request->actor->isAuthenticated()) {
            throw new PermissionDeniedException;
        }

        $user = $request->actor->getUser();

        $user->markNotificationsAsRead()->save();

        return $this->notifications->findByUser($user, $request->limit, $request->offset);
    }
}
