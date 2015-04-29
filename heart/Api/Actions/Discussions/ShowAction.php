<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\EditDiscussionCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Commands\DeleteDiscussionCommand;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;
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
    public function __construct()
    {
        global $qdiscuss_params, $qdiscuss_actor;
        $this->params = $qdiscuss_params;
        $this->actor = $qdiscuss_actor;
        $this->discussions = new DiscussionRepository;
        $this->posts = new PostRepository;
    }

    /**
     * Show a single discussion.
     *
     * @return Response
     */
    public function get($id)
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

    public function put($id)
    {
        global $qdiscuss_params, $qdiscuss_actor;
        $user = $qdiscuss_actor->getUser();
        $params = $this->post_data();
        
        if ($data = array_except($params->get('data'), ['readNumber'])) {
            try {
                $command = new EditDiscussionCommand($id, $user);
                $this->hydrate($command, $params->get('data'));
                $discussion = $this->dispatch($command, $params);
                } catch (PermissionDeniedException $e) {
                // Temporary fix. See @todo below
                $discussion = \Qdiscuss\Core\Models\Discussion::find($id);
            }
        }

        if ($readNumber = $params->get('data.readNumber')) {
            $command = new ReadDiscussionCommand($id, $user, $readNumber);
            $this->dispatch($command, $params);
        }

        $serializer = new DiscussionSerializer(['addedPosts', 'addedPosts.user']);
        $document = $this->document()->setData($serializer->resource($discussion));

        echo $this->respondWithDocument($document);exit();
    }

    /**
     * Delete a discussion.
     *
     * @param  int $id discussion's id
     * @return  Response
     */
    public function delete($id)
    {
        $discussionId = $id;
        $params = [];

        $command = new DeleteDiscussionCommand($discussionId, $this->actor->getUser());
        $this->dispatch($command, $params);

        echo $this->respondWithoutContent();exit();

    }
}
