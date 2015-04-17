<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Commands\RegisterUserCommand;
use Qdiscuss\Core\Actions\ApiParams;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\UserSerializer;

class CreateAction extends BaseAction
{
    public function __construct()
    {
        global $qdiscuss_actor;
        $this->user = $qdiscuss_actor;
    }
    /**
     * Register a user.
     *
     * @return Response
     */
    public function run()
    {
        
        $params = $this->post_data();
        $username = $params->get('data.username');
        $email    = $params->get('data.email');
        $password = $params->get('data.password');

        $command = new RegisterUserCommand($username, $email, $password, $this->actor->getUser(), app('qdiscuss.forum'));
        $user = $this->dispatch($command, $params);

        $serializer = new UserSerializer;
        $document = $this->document()->setData($serializer->resource($user));

        return $this->respondWithDocument($document, 201);
    }
}
