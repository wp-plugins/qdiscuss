<?php namespace Qdiscuss\Actions;

use Illuminate\Contracts\Bus\Dispatcher;
use Qdiscuss\Core\Commands\GenerateAccessTokenCommand;
use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepositoryInterface;
use Qdiscuss\Core\Actions\BaseAction;

class TokenAction extends BaseAction
{
    protected $users;

    public function __construct(UserRepositoryInterface $users, Dispatcher $bus)
    {
        $this->users = $users;
        $this->bus = $bus;
    }

    /**
     * Log in and return a token.
     *
     * @return Json
     */
    public function run(ApiParams $params)
    {
        $identification = $params->get('identification');
        $password = $params->get('password');

        $user = $this->users->findByIdentification($identification);

        if (! $user || ! $user->checkPassword($password)) {
            return $this->respondWithError('invalidCredentials', 401);
        }

        $command = new GenerateAccessTokenCommand($user->id);
        $token = $this->dispatch($command, $params);

        return json_encode(array(
                'token' => $token->id,
                'userId' => $user->id,
        ));
    }
}
