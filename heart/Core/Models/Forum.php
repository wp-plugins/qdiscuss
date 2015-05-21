<?php namespace Qdiscuss\Core\Models;

use Tobscure\Permissible\Permissible;

class Forum extends BaseModel
{
	use Permissible;
	
	public function getTitleAttribute()
	{
		return Core::config('forum_title');
	}
}
