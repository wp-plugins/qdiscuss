<?php namespace Qdiscuss\Api\Actions\Posts;

use Qdiscuss\Core\Commands\DeletePostCommand;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Core\Actions\BaseAction;

class DeleteAction extends BaseAction
{
    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
    }

    /**
     * Delete a post.
     *
     * @param   int $id post's id
     * @return   json
     */
    public function run($id)
    {
        $postId = $id;
       
        $command = new DeletePostCommand($postId, $this->actor->getUser());
        $this->dispatch($command, $params);

        echo $this->respondWithoutContent();exit();
    }
}
