<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Search\Discussions\DiscussionSearchCriteria;
use Qdiscuss\Core\Search\Discussions\DiscussionSearcher;
use Qdiscuss\Core\Repositories\EloquentDiscussionRepository;
use Qdiscuss\Core\Repositories\EloquentPostRepository;
use Qdiscuss\Api\Serializers\DiscussionSerializer;
use Qdiscuss\Core\Commands\StartDiscussionCommand;
use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Qdiscuss\Core\Actions\BaseAction;

class IndexAction extends BaseAction {

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		global $qdiscuss_params, $qdiscuss_actor, $qdiscuss_app;
		$this->params = $qdiscuss_params;
		$this->actor = $qdiscuss_actor;
		$this->searcher = new DiscussionSearcher($qdiscuss_app['qdiscuss.discussions.gambits'], new EloquentDiscussionRepository,  new EloquentPostRepository);
	}

	public function get()
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

	/**
	 * Start a new discussion.
	 *
	 * @return Response
	 */
	public function post()
	{

		$params = $this->post_data();

		$title = $params->get('data.title');
		$content = $params->get('data.content');
		$user = $this->actor->getUser();

		$command = new StartDiscussionCommand($title, $content, $user, app('qdiscuss.forum'));
		$discussion = $this->dispatch($command, $params);

		if ($user->exists) {
		    $command = new ReadDiscussionCommand($discussion->id, $user, 1);
		    $this->dispatch($command, $params);
		}

		$serializer = new DiscussionSerializer(['posts']);
		$document = $this->document()->setData($serializer->resource($discussion));

		echo $this->respondWithDocument($document);exit;
		
	}

}
