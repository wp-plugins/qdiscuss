<?php namespace Qdiscuss\Core\Repositories;

use Qdiscuss\Core\Models\User;

interface DiscussionRepositoryInterface
{
    /**
     * Get a new query builder for the discussions table.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query();

    /**
     * Find a discussion by ID, optionally making sure it is visible to a certain
     * user, or throw an exception.
     *
     * @param  integer  $id
     * @param  \Qdiscuss\Core\Models\User  $user
     * @return \Qdiscuss\Core\Models\Discussion
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, User $user = null);

    /**
     * Get the IDs of discussions which a user has read completely.
     *
     * @param  \Qdiscuss\Core\Models\User  $user
     * @return array
     */
    public function getReadIds(User $user);
}
