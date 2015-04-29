<?php namespace Qdiscuss\Actions\Groups;

use Qdiscuss\Core\Models\Group;
use Qdiscuss\Api\Serializers\GroupSerializer;
use Qdiscuss\Core\Actions\BaseAction;

class IndexAction extends BaseAction
{
	public function __construct()
	{
		# code...
	}

	public function get()
	{
	    $groups = Group::get();

	    $serializer = new GroupSerializer;
	    $this->document->setData($serializer->collection($groups));

	    header("Content-type: application/json");
	    echo $this->respondWithDocument();exit;
	}
}
