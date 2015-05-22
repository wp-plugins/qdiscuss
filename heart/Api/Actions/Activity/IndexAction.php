<?php namespace Qdiscuss\Api\Actions\Activity;

use Qdiscuss\Core\Repositories\UserRepositoryInterface;
// use Qdiscuss\Core\Repositories\ActivityRepositoryInterface; @todo
use Qdiscuss\Core\Repositories\EloquentActivityRepository as ActivityRepositoryInterface;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
    /**
     * @var \Qdiscuss\Core\Repositories\UserRepositoryInterface
     */
    protected $users;

    /**
     * @var \Qdiscuss\Core\Repositories\ActivityRepositoryInterface
     */
    protected $activity;

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public static $serializer = 'Qdiscuss\Api\Serializers\ActivitySerializer';

    /**
     * The relationships that are available to be included, and which ones are
     * included by default.
     *
     * @var array
     */
    public static $include = [
        'subject' => true,
        'subject.user' => true,
        'subject.discussion' => true
    ];

    /**
     * The relations that are linked by default.
     *
     * @var array
     */
    public static $link = ['user'];

    /**
     * Instantiate the action.
     *
     * @param \Qdiscuss\Core\Repositories\UserRepositoryInterface $users
     * @param \Qdiscuss\Core\Repositories\ActivityRepositoryInterface $activity
     */
    public function __construct(UserRepositoryInterface $users, ActivityRepositoryInterface $activity)
    {
        $this->users = $users;
        $this->activity = $activity;
    }

    /**
     * Get the activity results, ready to be serialized and assigned to the
     * document response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        $actor = $request->actor->getUser();

        $user = $this->users->findOrFail($request->get('users'), $actor);

        return $this->activity->findByUser($user->id, $actor, $request->limit, $request->offset, $request->get('type'))
             ->load($request->include);
    }
}
