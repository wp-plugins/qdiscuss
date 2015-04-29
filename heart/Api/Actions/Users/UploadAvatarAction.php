<?php namespace Qdiscuss\Api\Actions\Users;

use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Commands\UploadAvatarCommand;
use Qdiscuss\Api\Serializers\UserSerializer;
use Qdiscuss\Core\Support\FileUpload;

class UploadAvatarAction extends BaseAction
{
    public function __construct()
    {
            global $qdiscuss_actor;
            $this->actor = $qdiscuss_actor;
    }

    public function post($id)
    {
        $userId = $id;
        $file = $_FILES['avatar'];
        $routeParams = [];
     
        $file = new FileUpload($file);
        
        $user = $this->dispatch(
               new UploadAvatarCommand($userId, $file, $this->actor->getUser()),
               $routeParams
        );

        $serializer = new UserSerializer;
        $document = $this->document()->setData($serializer->resource($user));

        echo $this->respondWithDocument($document);exit();
    }

    public function handle()
    {
        
    }
}
