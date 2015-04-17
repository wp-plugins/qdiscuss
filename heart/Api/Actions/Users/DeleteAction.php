<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\DeleteUserCommand;
use Qdiscuss\Core\Actions\BaseAction;

class DeleteAction extends BaseAction
{

    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
    }

    /**
     * Delete a user.
     *
     * @param  $id
     * @return   json
     */
    public function run($id)
    {
        $userId = $id;

        $command = new DeleteUserCommand($userId, $this->actor->getUser());
        $this->dispatch($command, $params);

        echo  $this->respondWithoutContent();exit();
    }
}
