<?php namespace Qdiscuss\Forum\Actions;

// use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Bus\Dispatcher;
use Qdiscuss\Forum\Commands\GenerateAccessTokenCommand;
use Qdiscuss\Forum\Repositories\EloquentUserRepository as UserRepositoryInterface;
use Qdiscuss\Forum\Actions\BaseAction;

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
     * @return Response
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

        // return new JsonResponse([
        //     'token' => $token->id,
        //     'userId' => $user->id
        // ]);
        // neychang just simple return json
        return json_encode(array(
                'token' => $token->id,
                'userId' => $user->id,
        ));
    }
}
