<?php namespace Qdiscuss\Core\Events;

use Qdiscuss\Core\Search\Users\UserSearcher;

class UserSearchWillBePerformed
{
	public $searcher;

	public $criteria;
	
	public function __construct(UserSearcher $searcher, $criteria)
	{
		$this->searcher = $searcher;
		$this->criteria = $criteria;
	}
}