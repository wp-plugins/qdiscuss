<?php namespace Qdiscuss\Core\Models;

trait MergeableTrait
{
	public function saveAfter(BaseModel $previous)
	{
		if ($previous instanceof static) {
			if ($this->mergeInto($previous)) {
				$previous->save();
			} else {
				$previous->delete();
			}
			return $previous;
		}
		$this->save();
		return $this;
	}

	abstract protected function mergeInto(BaseModel $previous);
}