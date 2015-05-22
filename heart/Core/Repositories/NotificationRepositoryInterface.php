<?php namespace Qdiscuss\Core\Repositories;

use Qdiscuss\Core\Models\User;

interface NotificationRepositoryInterface
{
    public function findByUser(User $user, $count = null, $start = 0);
}
