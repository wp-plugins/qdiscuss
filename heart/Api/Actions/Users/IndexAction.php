<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Search\Users\UserSearchCriteria;
use Qdiscuss\Core\Search\Users\UserSearcher;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
    /**
     * The user searcher.
     *
     * @var \Qdiscuss\Core\Search\Discussions\UserSearcher
     */
    protected $searcher;

    /**
     * Instantiate the action.
     *
     * @param  \Qdiscuss\Core\Search\Discussions\UserSearcher  $searcher
     */
    public function __construct(UserSearcher $searcher)
    {
        $this->searcher = $searcher;
    }

    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public static $serializer = 'Qdiscuss\Api\Serializers\UserSerializer';

    /**
     * The relationships that are available to be included, and which ones are
     * included by default.
     *
     * @var array
     */
    public static $include = [
        'groups' => true
    ];

    /**
     * The fields that are available to be sorted by.
     *
     * @var array
     */
    public static $sortFields = ['username', 'postsCount', 'discussionsCount', 'lastSeenTime', 'joinTime'];

    /**
     * Get the user results, ready to be serialized and assigned to the
     * document response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        $criteria = new UserSearchCriteria(
            $request->actor->getUser(),
            $request->get('q'),
            $request->sort
        );

        $results = $this->searcher->search($criteria, $request->limit, $request->offset, $request->include);

        if (($total = $results->getTotal()) !== null) {
            $response->content->addMeta('total', $total);
        }

        static::addPaginationLinks($response, $request, route('Qdiscuss.api.users.index'), $total ?: $results->areMoreResults());

        return $results->getUsers();
    }
}
