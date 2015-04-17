<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Core\Repositories\EloquentDiscussionRepository as DiscussionRepository;
use Qdiscuss\Core\Repositories\EloquentPostRepository as PostRepository;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Api\Actions\Posts\GetsPosts;
use Qdiscuss\Api\Serializers\DiscussionSerializer;

class ShowAction extends BaseAction
{
    use GetsPosts;

    /**
     * The discussion repository.
     *
     * @var DiscussionRepository
     */
    protected $discussions;

    /**
     * The post repository.
     *
     * @var PostRepository
     */
    protected $posts;

    /**
     * Instantiate the action.
     *
     * @param PostRepository $posts
     */
    public function __construct(ApiParams $params, Actor $actor, DiscussionRepository $discussions, PostRepository $posts)
    {
        $this->params = $params;
        $this->actor = $actor;
        $this->discussions = $discussions;
        $this->posts = $posts;
    }

    /**
     * Show a single discussion.
     *
     * @return Response
     */
    public function run($id)
    {
        $include = $this->params->included(['startPost', 'lastPost', 'posts']);
        $user = $this->actor->getUser();
        $discussion = $this->discussions->findOrFail($id, $user);

        $discussion->posts_ids = $discussion->posts()->whereCan($user, 'view')->get(['id'])->fetch('id')->all();
        
        if (in_array('posts', $include)) {
            $relations = ['user', 'user.groups', 'editUser', 'hideUser'];
            $discussion->posts = $this->getPosts($this->params, ['discussion_id' => $discussion->id])->load($relations);

            $include = array_merge($include, array_map(function ($relation) {
                return 'posts.'.$relation;
            }, $relations));
        }

        $serializer = new DiscussionSerializer($include, ['posts']);

        $document = $this->document()->setData($serializer->resource($discussion));
        
        echo  $this->respondWithDocument($document);exit;
    }
}
