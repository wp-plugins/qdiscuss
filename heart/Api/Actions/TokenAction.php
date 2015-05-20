<?php namespace Qdiscuss\Api\Actions;

use Qdiscuss\Api\Request;
use Qdiscuss\Core\Commands\GenerateAccessTokenCommand;
use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Slim\Http\JsonResponse;
use Illuminate\Contracts\Bus\Dispatcher;

class TokenAction implements ActionInterface
{
	protected $users;

	protected $bus;

	public function __construct(UserRepositoryInterface $users, Dispatcher $bus)
	{
		$this->users = $users;
		$this->bus = $bus;
	}

	/**
	 * Log in and return a token.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @return \Qdiscuss\Api\Response
	 */
	public function handle(Request $request)
	{
		$identification = $request->get('identification');
		$password = $request->get('password');

		$user = $this->users->findByIdentification($identification);

		if (! $user || ! $user->checkPassword($password)) {
			return;
			// throw an exception
			// return $this->respondWithError('invalidCredentials', 401);
			return new JsonResponse(null, 401);
		}

		$token = $this->bus->dispatch(
			new GenerateAccessTokenCommand($user->id)
		);

		return new JsonResponse([
			'token' => $token->id,
			'userId' => $user->id
		]);
	}
}