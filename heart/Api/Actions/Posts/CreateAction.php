<?php namespace Qdiscuss\Api\Actions\Posts;

use Qdiscuss\Core\Commands\PostReplyCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\PostSerializer;

class CreateAction extends BaseAction
{
    protected $actor;

    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
    }
    /**
     * Reply to a discussion.
     *
     * @return Response
     */
    public function run()
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
