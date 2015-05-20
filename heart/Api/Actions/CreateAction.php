<?php namespace Qdiscuss\Api\Actions;

use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

abstract class CreateAction extends SerializeResourceAction
{
	/**
	 * Delegate creation of the resource, and set a 201 Created status code on
	 * the response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Model
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		$response->setStatus(201);
		return $this->create($request, $response);
	}
	/**
	 * Create the resource.
	 *
	 * @return \Qdiscuss\Core\Models\Model
	 */
	abstract protected function create(JsonApiRequest $request, JsonApiResponse $response);
	
}