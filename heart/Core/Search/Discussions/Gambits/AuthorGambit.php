<?php namespace Qdiscuss\Core\Search\Discussions\Gambits;

use Qdiscuss\Core\Repositories\UserRepositoryInterface as UserRepository;
use Qdiscuss\Core\Search\SearcherInterface;
use Qdiscuss\Core\Search\GambitAbstract;

class AuthorGambit extends GambitAbstract
{
    /**
     * The gambit's regex pattern.
     * @var string
     */
    protected $pattern = 'author:(.+)';

    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function conditions($matches, SearcherInterface $searcher)
    {
        $username = trim($matches[1], '"');

        $id = $this->users->getIdForUsername($username);

        $searcher->query()->where('start_user_id', $id);
    }
}
