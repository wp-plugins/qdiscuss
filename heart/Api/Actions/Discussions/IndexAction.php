<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Search\Discussions\DiscussionSearchCriteria;
use Qdiscuss\Core\Search\Discussions\DiscussionSearcher;
use Qdiscuss\Api\Serializers\DiscussionSerializer;
use Qdiscuss\Core\Actions\BaseAction;

class IndexAction extends BaseAction {

	/**
	 * Constructor
	 *
	 */
	public function __construct(DiscussionSearcher $searcher)
	{
		global $qdiscuss_params, $qdiscuss_actor;
		$this->params = $qdiscuss_params;
		$this->actor = $qdiscuss_actor;
		$this->searcher = $searcher;
	}

	public function run()
	{
		$query   = $this->params->get('q');
		$start     = $this->params->start();
		$include = $this->params->included(['startUser', 'lastUser', 'startPost', 'lastPost', 'relevantPosts']);
		$count   = $this->params->count(20, 50);
		$sort      = $this->params->sort(['', 'lastPost', 'replies', 'created']);
		
		$criteria = new DiscussionSearchCriteria($this->actor->getUser(), $query, $sort['field'], $sort['order']);

		$load = array_merge($include, ['state']);
		$results = $this->searcher->search($criteria, $count, $start, $load);
		$document = $this->document();

		if (($total = $results->getTotal()) !== null) {
		    $document->addMeta('total', $total);
		}

		
		if ($results->areMoreResults()) {
		    $start += $count;
		    $sort = $sort['string'];
		    $input = array_filter(compact('query', 'sort', 'start', 'count')) + ['include' => implode(',', $include)];
		    $moreUrl = $this->buildUrl('discussions.index', [], $input);
		} else {
		    $moreUrl = '';
		}
		$document->addMeta('moreUrl', $moreUrl);

		$serializer = new DiscussionSerializer($include);
		$document->setData($serializer->collection($results->getDiscussions()));

		echo $this->respondWithDocument($document);exit();
	}
}
