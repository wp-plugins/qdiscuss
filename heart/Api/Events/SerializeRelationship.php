<?php namespace Qdiscuss\Api\Events;

class SerializeRelationship
{
    public $serializer;

    public $name;

    public function __construct($serializer, $name)
    {
        $this->serializer = $serializer;
        $this->name = $name;
    }
}
