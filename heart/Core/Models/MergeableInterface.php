<?php namespace Qdiscuss\Core\Models;

interface MergeableInterface
{
	public function saveAfter(BaseModel $previous);
}