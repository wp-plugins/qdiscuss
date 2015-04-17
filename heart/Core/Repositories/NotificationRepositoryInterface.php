<?php namespace Qdiscuss\Core\Repositories;

interface NotificationRepositoryInterface
{
    public function findByUser($userId, $count = null, $start = 0);
}
