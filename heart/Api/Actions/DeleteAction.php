<?php namespace Qdiscuss\Api\Actions;

use Qdiscuss\Api\Request;
use Slim\Http\Response;

abstract class DeleteAction extends JsonApiAction
{
	/**
	 * Delegate deletion of the resource, and return a 204 No Content
	 * response.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @return \Qdiscuss\Api\Response
	 */
	public function respond(Request $request)
	{
		$this->delete($request, $response = new Response('', 204));
		return $response;
	}
	/**
	 * Delete the resource.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @param \Qdiscuss\Api\Response $response
	 * @return void
	 */
	abstract protected function delete(Request $request, Response $response);
}