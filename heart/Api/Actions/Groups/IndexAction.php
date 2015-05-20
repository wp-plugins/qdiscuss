<?php namespace Qdiscuss\Api\Actions\Groups;

use Qdiscuss\Core\Models\Group;
use Qdiscuss\Api\Actions\SerializeCollectionAction;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class IndexAction extends SerializeCollectionAction
{
    /**
     * The name of the serializer class to output results with.
     *
     * @var string
     */
    public static $serializer = 'Qdiscuss\Api\Serializers\GroupSerializer';

    /**
     * Get the groups, ready to be serialized and assigned to the document
     * response.
     *
     * @param \Qdiscuss\Api\JsonApiRequest $request
     * @param \Qdiscuss\Api\JsonApiResponse $response
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function data(JsonApiRequest $request, JsonApiResponse $response)
    {
        return Group::get();
    }
}
