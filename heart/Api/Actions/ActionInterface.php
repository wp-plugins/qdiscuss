<?php namespace Qdiscuss\Api\Actions;

use Qdiscuss\Api\Request;

interface ActionInterface
{
	/**
	 * Handle a request to the API, returning an HTTP response.
	 *
	 * @param \Qdiscuss\Api\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function handle(Request $request);
	
}