<?php namespace Qdiscuss\Api\Actions\Users;

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

    public function __construct(ApiParams $params, Actor $actor, UserRepositoryInterface $users)
    {
        $this->params = $params;
        $this->actor = $actor;
        $this->users = $users;
    }

    /**
     * Show a single user.
     *
     * @param  string id user's id
     * @return Response
     */
    public function run($id)
    {
        if (! is_numeric($id)) {
            $id = $this->users->getIdForUsername($id);
        }

        $user = $this->users->findOrFail($id, $this->actor->getUser());

        $serializer = new UserSerializer(['groups']);
        $document = $this->document()->setData($serializer->resource($user));

        echo $this->respondWithDocument($document);exit();
    }
}
