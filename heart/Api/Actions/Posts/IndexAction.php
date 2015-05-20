<?php namespace Qdiscuss\Api\Actions\Posts;

use Qdiscuss\Core\Repositories\PostRepositoryInterface;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
    use GetsPosts;

    /**
     * @var \Qdiscuss\Core\Repositories\PostRepositoryInterface
     */
    protected $posts;

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public static $serializer = 'Qdiscuss\Api\Serializers\PostSerializer';

    /**
     * The relationships that are available to be included, and which ones are
     * included by default.
     *
     * @var array
     */
    public static $include = [
        'user' => true,
        'user.groups' => true,
        'editUser' => true,
        'hideUser' => true,
        'discussion' => true
    ];

    /**
     * Instantiate the action.
     *
     * @param \Qdiscuss\Core\Repositories\PostRepositoryInterface $posts
     */
    public function __construct(PostRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    /**
     * Get the post results, ready to be serialized and assigned to the
     * document response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        $postIds = (array) $request->get('ids');
        $user = $request->actor->getUser();

        if (count($postIds)) {
            $posts = $this->posts->findByIds($postIds, $user);
        } else {
            if ($discussionId = $request->get('discussions')) {
                $where['discussion_id'] = $discussionId;
            }
            if ($number = $request->get('number')) {
                $where['number'] = $number;
            }
            if ($userId = $request->get('users')) {
                $where['user_id'] = $userId;
            }
            $posts = $this->getPosts($request, $where);
        }

        return $posts;
    }
}
