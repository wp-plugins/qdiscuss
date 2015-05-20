<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Search\GambitManager;

class RegisterDiscussionGambits
{
	public $gambits;

	public function __construct(GambitManager $gambits)
	{
		$this->gambits = $gambits;
	}

}