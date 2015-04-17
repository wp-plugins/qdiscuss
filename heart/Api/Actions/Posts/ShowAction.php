<?php namespace Qdiscuss\Api\Actions\Posts;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Qdiscuss\Core\Repositories\PostRepositoryInterface;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\PostSerializer;

class ShowAction extends BaseAction
{
    protected $posts;

    public function __construct(PostRepositoryInterface $posts)
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
        $this->posts = $posts;
    }

    /**
     * Show a single post by ID.
     *
     * @param  int $id post'id
     * @return Response
     */
    public function run($id)
    {
        $posts = $this->posts->findOrFail($id, $this->actor->getUser());

        $include = $params->included(['discussion', 'replyTo']);
        $relations = array_merge(['user', 'editUser', 'hideUser'], $include);
        $posts->load($relations);

        $serializer = new PostSerializer($relations);
        $document = $this->document()->setData($serializer->resource($posts->first()));

        echo $this->respondWithDocument($document);exit();
    }
}
