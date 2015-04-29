<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Repositories\EloquentUserRepository;
use Qdiscuss\Core\Search\Users\UserSearchCriteria;
use Qdiscuss\Core\Search\Users\UserSearcher;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\UserSerializer;

class IndexAction extends BaseAction
{
    /**
     * The user searcher.
     *
     * @var \Qdiscuss\Search\Discussions\UserSearcher
     */
    protected $searcher;

    /**
     * Instantiate the action.
     *
     * @param  \Qdiscuss\Search\Discussions\UserSearcher  $searcher
     */
    public function __construct()
    {
        global $qdiscuss_actor, $qdiscuss_params, $qdiscuss_app;
        $this->actor = $qdiscuss_actor;
        $this->params = $qdiscuss_params;
        $this->searcher = new UserSearcher($qdiscuss_app['qdiscuss.discussions.gambits'], new EloquentUserRepository);
    }

    /**
     * Show a list of users.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $params = $this->params;
        $query   = $params->get('q');
        $start   = $params->start();
        $include = $params->included(['groups']);
        $count   = $params->count(20, 50);
        $sort    = $params->sort(['', 'username', 'posts', 'discussions', 'lastActive', 'created']);

        $relations = array_merge(['groups'], $include);

        $criteria = new UserSearchCriteria($this->actor->getUser(), $query, $sort['field'], $sort['order']);

        $results = $this->searcher->search($criteria, $count, $start, $relations);

        $document = $this->document();

        if (($total = $results->getTotal()) !== null) {
            $document->addMeta('total', $total);
        }

        if ($results->areMoreResults()) {
            $start += $count;
            $include = implode(',', $include);
            $sort = $sort['string'];
            $input = array_filter(compact('query', 'sort', 'start', 'count', 'include'));
            $moreUrl = $this->buildUrl('users.index', [], $input);
        } else {
            $moreUrl = '';
        }
        $document->addMeta('moreUrl', $moreUrl);

        $serializer = new UserSerializer($relations);
        $document->setData($serializer->collection($results->getUsers()));

        echo $this->respondWithDocument($document);exit();
    }
}
