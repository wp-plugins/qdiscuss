<?php namespace Qdiscuss\Api\Serializers;

class NotificationSerializer extends BaseSerializer
{
    /**
     * The resource type.
     * @var string
     */
    protected $type = 'notifications';

    /**
     * Serialize attributes of an notification model for JSON output.
     *
     * @param Notification $notification The notification model to serialize.
     * @return array
     */
    protected function attributes($notification)
    {
        $attributes = [
            'id'   => (int) $notification->id,
            'contentType' => $notification->type,
            'content' => $notification->data,
            'time' => $notification->time->toRFC3339String(),
            'isRead' => (bool) $notification->is_read,
            'unreadCount' => $notification->unread_count
        ];

        return $this->extendAttributes($notification, $attributes);
    }

    public function user()
    {
        return $this->hasOne('Qdiscuss\Api\Serializers\UserBasicSerializer');
    }

    public function sender()
    {
        return $this->hasOne('Qdiscuss\Api\Serializers\UserBasicSerializer');
    }

    public function subject()
    {
        return $this->hasOne([
            'Qdiscuss\Core\Models\Discussion' => 'Qdiscuss\Api\Serializers\DiscussionSerializer',
            'Qdiscuss\Core\Models\CommentPost' => 'Qdiscuss\Api\Serializers\PostSerializer'
        ]);
    }
}
