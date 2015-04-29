<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\EditUserCommand;
use Qdiscuss\Core\Commands\DeleteDiscussionCommand;
use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepositoryInterface;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\UserSerializer;

class ShowAction extends BaseAction
{
    protected $params;

    protected $actor;

    protected $users;

    public function __construct()
    {
        global $qdiscuss_actor, $qdiscuss_params;
        $this->params = $qdiscuss_params;
        $this->actor = $qdiscuss_actor;
        $this->users = new UserRepositoryInterface;
    }

    /**
     * Show a single user.
     *
     * @param  string id user's id
     * @return Response
     */
    public function get($id)
    { 
       
        if (! is_numeric($id)) {
            $id = $this->users->getIdForUsername($id);
        }

        $user = $this->users->findOrFail($id, $this->actor->getUser());

        $serializer = new UserSerializer(['groups']);
        $document = $this->document()->setData($serializer->resource($user));

        echo $this->respondWithDocument($document);exit();
    }

    /**
     * Edit a user. Allows renaming the user, changing their email, and setting
     * their password.
     *
     * @return Response
     */
    public function put($id)
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
