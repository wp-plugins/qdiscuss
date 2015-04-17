<?php namespace Qdiscuss\Core\Search\Users\Gambits;

use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Qdiscuss\Core\Search\SearcherInterface;
use Qdiscuss\Core\Search\GambitAbstract;

class FulltextGambit extends GambitAbstract
{
    protected $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function apply($string, SearcherInterface $searcher)
    {
        $users = $this->users->getIdsForUsername($string, $searcher->user);

        $searcher->query()->whereIn('id', $users);

        $searcher->setDefaultSort($users);
    }
}
