<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Search\Discussions\DiscussionSearchCriteria;
use Qdiscuss\Core\Search\Discussions\DiscussionSearcher;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
	/**
	 * The discussion searcher.
	 *
	 * @var \Qdiscuss\Core\Search\Discussions\DiscussionSearcher
	 */
	protected $searcher;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\DiscussionSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		'startUser' => true,
		'lastUser' => true,
		'startPost' => false,
		'lastPost' => false,
		'relevantPosts' => false
	];

	/**
	 * The fields that are available to be sorted by.
	 *
	 * @var array
	 */
	public static $sortFields = ['lastTime', 'commentsCount', 'startTime'];

	/**
	 * Instantiate the action.
	 *
	 * @param \Qdiscuss\Core\Search\Discussions\DiscussionSearcher $searcher
	 */
	public function __construct(DiscussionSearcher $searcher)
	{
		$this->searcher = $searcher;
	}

	/**
	 * Get the discussion results, ready to be serialized and assigned to the
	 * document response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		global $qdiscuss_endpoint;
		$criteria = new DiscussionSearchCriteria(
			$request->actor->getUser(),
			$request->get('q'),
			$request->sort
		);

		$load = array_merge($request->include, ['state']);
		$results = $this->searcher->search($criteria, $request->limit, $request->offset, $load);

		if (($total = $results->getTotal()) !== null) {
			$response->content->addMeta('total', $total);
		}

		static::addPaginationLinks($response, $request, get_site_url() . '/' . $qdiscuss_endpoint . '/discussions', $total ?: $results->areMoreResults());

		return $results->getDiscussions();
		
	}
}
