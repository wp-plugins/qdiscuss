<?php namespace Qdiscuss\Api\Actions;

use Closure;
use Qdiscuss\Api\Request;
// use Illuminate\Http\JsonResponse;
use Slim\Http\Response as  JsonResponse;
use Qdiscuss\Core\Exceptions\ValidationFailureException;
use Qdiscuss\Core\Exceptions\PermissionDeniedException;

abstract class JsonApiAction implements ActionInterface
{
	/**
	 * Handle an API request and return an API response, handling any relevant
	 * (API-related) exceptions that are thrown.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @return \Qdiscuss\Api\Response
	 */
	public function handle(Request $request)
	{
		try {
			return $this->respond($request);
		} catch (ValidationFailureException $e) {
			$errors = [];
			foreach ($e->getErrors()->getMessages() as $field => $messages) {
				$errors[] = [
					'detail' => implode("\n", $messages),
					'path' => $field
				];
			}
			return new JsonResponse(['errors' => $errors], 422);
		} catch (PermissionDeniedException $e) {
			return new JsonResponse(null, 401);
		}
	}

	/**
	 * Handle an API request and return an API response.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @return \Qdiscuss\Api\Response
	 */
	abstract protected function respond(Request $request);
}