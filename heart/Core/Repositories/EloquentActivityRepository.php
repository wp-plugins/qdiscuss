<?php namespace Qdiscuss\Core\Repositories;

use Qdiscuss\Core\Models\Activity;
use Qdiscuss\Core\Models\User;

class EloquentActivityRepository implements ActivityRepositoryInterface
{
	public function findByUser($userId, User $viewer, $limit = null, $offset = 0, $type = null)
	{
		$query = Activity::where('user_id', $userId)
			->whereIn('type', array_keys(Activity::getTypes()))
			->orderBy('time', 'desc')
			->skip($offset)
			->take($limit);
		if ($type !== null) {
			$query->where('type', $type);
		}
		return $query->get();
	}
}