<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\StartDiscussionCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Api\Serializers\DiscussionSerializer;

class CreateAction extends BaseAction
{

    public function __construct()
    {
            global $qdiscuss_actor;
            $this->actor = $qdiscuss_actor;
    }
    /**
     * Start a new discussion.
     *
     * @return Response
     */
    public function run()
    {

        $params = $this->post_data();
       
        $title = $params->get('data.title');
        $content = $params->get('data.content');
        $user = $this->actor->getUser();

        $command = new StartDiscussionCommand($title, $content, $user, app('qdiscuss.forum'));
        $discussion = $this->dispatch($command, $params);

        if ($user->exists) {
            $command = new ReadDiscussionCommand($discussion->id, $user, 1);
            $this->dispatch($command, $params);
        }

        $serializer = new DiscussionSerializer(['posts']);
        $document = $this->document()->setData($serializer->resource($discussion));

        echo $this->respondWithDocument($document);exit;
    }
}
