<?php namespace Qdiscuss\Core\Search\Discussions\Gambits;

use Qdiscuss\Core\Repositories\PostRepositoryInterface;
use Qdiscuss\Core\Search\SearcherInterface;
use Qdiscuss\Core\Search\GambitAbstract;

class FulltextGambit extends GambitAbstract
{
    protected $posts;

    public function __construct(PostRepositoryInterface $posts)
    {
        $this->posts = $posts;
    }

    public function apply($string, SearcherInterface $searcher)
    {
        $posts = $this->posts->findByContent($string, $searcher->user);

        $discussions = [];
        foreach ($posts as $post) {
            $discussions[] = $id = $post->discussion_id;
            $searcher->addRelevantPost($id, $post->id);
        }
        $discussions = array_unique($discussions);

        $searcher->query()->whereIn('id', $discussions);

        $searcher->setDefaultSort(['id' => $discussions]);
    }
}
