<?php namespace Qdiscuss\Api\Actions\Posts;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Qdiscuss\Core\Repositories\EloquentPostRepository as PostRepositoryInterface;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Api\Serializers\PostSerializer;

class IndexAction extends BaseAction
{
    use GetsPosts;

    /**
     * The post repository.
     *
     * @var Post
     */
    protected $posts;

    /**
     * Instantiate the action.
     *
     * @param Post $posts
     */
    public function __construct(ApiParams $params, Actor $actor, PostRepositoryInterface $posts)
    {
        $this->params = $params;
        $this->actor = $actor;
        $this->posts = $posts;
    }

    /**
     * Show posts from a discussion, or by providing an array of IDs.
     *
     * @return Response
     */
    public function run()
    {
        $postIds = (array) $this->params->get('ids');
        $include = ['user', 'user.groups', 'editUser', 'hideUser', 'discussion'];
        $user = $this->actor->getUser();

        if (count($postIds)) {
            $posts = $this->posts->findByIds($postIds, $user);
        } else {
            if ($discussionId = $this->params->get('discussions')) {
                $where['discussion_id'] = $discussionId;
            }
            if ($userId = $this->params->get('users')) {
                $where['user_id'] = $userId;
            }
            $posts = $this->getPosts($this->params, $where, $user);
        }

        if (! count($posts)) {
            throw new ModelNotFoundException;
        }

        // Finally, we can set up the post serializer and use it to create
        // a post resource or collection, depending on how many posts were
        // requested.
        $serializer = new PostSerializer($include);
        $document = $this->document()->setData($serializer->collection($posts->load($include)));

        echo $this->respondWithDocument($document);exit;
    }
}
