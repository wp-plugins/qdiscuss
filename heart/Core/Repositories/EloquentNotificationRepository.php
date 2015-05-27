<?php namespace Qdiscuss\Core\Repositories;

use Qdiscuss\Core\Models\Notification;
use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Models\User;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function findByUser(User $user, $limit = null, $offset = 0)
    {
        $prefix = DB::getTablePrefix();
        $primaries = Notification::select(DB::raw('MAX(id) AS id'), DB::raw('SUM(is_read = 0) AS unread_count'))
            ->where('user_id', $user->id)
            ->whereIn('type', array_filter(array_keys(Notification::getTypes()), [$user, 'shouldAlert']))
            ->where('is_deleted', false)
            ->groupBy('type', 'subject_id')
            ->orderBy('time', 'desc')
            ->skip($offset)
            ->take($limit);
    
        return Notification::with('subject')
            ->select('notifications.*', 'p.unread_count')
            ->mergeBindings($primaries->getQuery())
            ->join(DB::raw('('.$primaries->toSql().') ' .  $prefix . 'p'), 'notifications.id', '=', 'p.id')
            ->orderBy('time', 'desc')
            ->get();
    }
}
