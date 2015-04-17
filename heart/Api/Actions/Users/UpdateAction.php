<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\EditUserCommand;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\UserSerializer;

class UpdateAction extends BaseAction
{

    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
    }
    /**
     * Edit a user. Allows renaming the user, changing their email, and setting
     * their password.
     *
     * @return Response
     */
    public function run($id)
    {
        $params = $this->post_data();
        $userId = $id;
        
        $command = new EditUserCommand($userId, $this->actor->getUser());
        $this->hydrate($command, $params->get('data'));
        $user = $this->dispatch($command, $params);

        $serializer = new UserSerializer;
        $document = $this->document()->setData($serializer->resource($user));

        echo   $this->respondWithDocument($document); exit();
    }
}
