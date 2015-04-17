<?php namespace Qdiscuss\Core\Repositories;

use Qdiscuss\Core\Models\Notification;
use Illuminate\Database\Capsule\Manager as DB; 

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function findByUser($userId, $count = null, $start = 0)
    {
        global $wpdb, $qdiscuss_config;

        $prefix = $wpdb->prefix . $qdiscuss_config['database']['qd_prefix'];//just fix the table prefix in DB::raw by neychang
        
        $primaries = Notification::select(DB::raw('MAX(id) AS id'), DB::raw('SUM(is_read = 0) AS unread_count'))
            ->where('user_id', $userId)
            ->whereIn('type', array_keys(Notification::getTypes()))
            ->groupBy('type', 'subject_id')
            ->orderBy('time', 'desc')
            ->skip($start)
            ->take($count);
    
        return Notification::with('subject')
            ->select('notifications.*', 'p.unread_count')
            ->mergeBindings($primaries->getQuery())
            ->join(DB::raw('('.$primaries->toSql().') ' .  $prefix . 'p'), 'notifications.id', '=', 'p.id')
            ->orderBy('time', 'desc')
            ->get();
    }
}
