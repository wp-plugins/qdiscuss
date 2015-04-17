<?php namespace Qdiscuss\Actions\Groups;

use Qdiscuss\Core\Models\Group;
use Qdiscuss\Actions\Base;
use Qdiscuss\Api\Serializers\GroupSerializer;

class Index extends Base
{
    protected function run()
    {
        $groups = Group::get();

        $serializer = new GroupSerializer;
        $this->document->setData($serializer->collection($groups));

        return $this->respondWithDocument();
    }
}
