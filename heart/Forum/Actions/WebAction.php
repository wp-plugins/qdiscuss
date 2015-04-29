<?php namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Core\Support\Action;
use Qdiscuss\Forum\Events\CommandWillBeDispatched;

abstract class WebAction extends Action
{
	protected function dispatch($command, $params = [])
	{
		event(new CommandWillBeDispatched($command, $params));
		return $this->bus->dispatch($command);
	}

	protected function respondJson($data)
	{
		header("Content-type: application/json");
		echo json_encode($data);exit;
	}
}
