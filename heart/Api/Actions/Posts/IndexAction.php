<?php namespace Qdiscuss\Api\Actions\Posts;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Qdiscuss\Core\Repositories\EloquentPostRepository as PostRepositoryInterface;
use Qdiscuss\Core\Commands\PostReplyCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
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
    public function __construct()
    {
        global $qdiscuss_actor, $qdiscuss_params;
        $this->params = $qdiscuss_params;
        $this->actor = $qdiscuss_actor;
        $this->posts = new PostRepositoryInterface;
    }

    /**
     * Show posts from a discussion, or by providing an array of IDs.
     *
     * @return Response
     */
    public function get()
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

    /**
     * Reply to a discussion.
     *
     * @return Response
     */
    public function post()
    {
        $params = $this->post_data();
        $user = $this->actor->getUser();
        
        $discussionId = $params->get('data.links.discussion.linkage.id');
        $content = $params->get('data.content');
        
        $command = new PostReplyCommand($discussionId, $content, $user);
        $post = $this->dispatch($command, $params);

        
        if ($user->exists) {
            $command = new ReadDiscussionCommand($discussionId, $user, $post->number);
            $this->dispatch($command, $params);
        }

        $serializer = new PostSerializer;
        $document = $this->document()->setData($serializer->resource($post));

        echo $this->respondWithDocument($document, 201);exit();
    }
}
