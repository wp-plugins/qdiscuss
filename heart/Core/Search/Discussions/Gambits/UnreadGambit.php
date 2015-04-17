<?php namespace Qdiscuss\Core\Search\Discussions\Gambits;

use Qdiscuss\Core\Repositories\DiscussionRepositoryInterface as DiscussionRepository;
use Qdiscuss\Core\Search\SearcherInterface;
use Qdiscuss\Core\Search\GambitAbstract;

class UnreadGambit extends GambitAbstract
{
    /**
     * The gambit's regex pattern.
     * @var string
     */
    protected $pattern = 'unread:(true|false)';

    protected $discussions;

    public function __construct(DiscussionRepository $discussions)
    {
        $this->discussions = $discussions;
    }

    protected function conditions($matches, SearcherInterface $searcher)
    {
        $user = $searcher->user;

        if ($user->exists) {
            $readIds = $this->discussions->getReadIds($user);

            if ($matches[1] === 'true') {
                $searcher->query()->whereNotIn('id', $readIds)->where('last_time', '>', $user->read_time ?: 0);
            } else {
                $searcher->query()->whereIn('id', $readIds)->orWhere('last_time', '<=', $user->read_time ?: 0);
            }
        }
    }
}
