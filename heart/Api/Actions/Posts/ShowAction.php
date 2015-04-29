<?php namespace Qdiscuss\Api\Actions\Posts;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Qdiscuss\Core\Repositories\EloquentPostRepository as PostRepositoryInterface;
use Qdiscuss\Core\Commands\EditPostCommand;
use Qdiscuss\Core\Commands\DeletePostCommand;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Api\Serializers\PostSerializer;

class ShowAction extends BaseAction
{
    protected $posts;

    public function __construct()
    {
        global $qdiscuss_actor;
        $this->actor = $qdiscuss_actor;
        $this->posts = new PostRepositoryInterface;
    }

    /**
     * Show a single post by ID.
     *
     * @param  int $id post'id
     * @return Response
     */
    public function get($id)
    {
         echo $this->process($id);exit;
    }

    /**
     * Show a single post by ID.
     *
     * @param  int $id post'id
     * @return Response
     */
    public function put($id)
    {
        $postId = $id;
        $params = $this->post_data();

        $command = new EditPostCommand($postId, $this->actor->getUser());
        $this->hydrate($command, $params->get('data'));
        $post = $this->dispatch($command, $params);

        $serializer = new PostSerializer;
        $document = $this->document()->setData($serializer->resource($post));

        echo $this->respondWithDocument($document);exit();
        
    }
        
        /**
         * Delete a post.
         *
         * @param   int $id post's id
         * @return   json
         */
        public function delete($id)
        {
            $postId = $id;
           
            $command = new DeletePostCommand($postId, $this->actor->getUser());
            $this->dispatch($command, []);

            echo $this->respondWithoutContent();exit();
       
     }

    protected function process($id)
    {
        $params = $this->post_data();

        $posts = $this->posts->findOrFail($id, $this->actor->getUser());

        $include = $params->included(['discussion', 'replyTo']);
        $relations = array_merge(['user', 'editUser', 'hideUser'], $include);
        $posts->load($relations);

        $serializer = new PostSerializer($relations);
        $document = $this->document()->setData($serializer->resource($posts->first()));

        return $this->respondWithDocument($document);
    }
}
