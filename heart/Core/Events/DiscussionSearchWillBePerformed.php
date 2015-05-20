<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Search\Discussions\DiscussionSearcher;

class DiscussionSearchWillBePerformed
{
	public $searcher;

	public $criteria;

	public function __construct(DiscussionSearcher $searcher, $criteria)
	{
		$this->searcher = $searcher;
		$this->criteria = $criteria;
	}
}
