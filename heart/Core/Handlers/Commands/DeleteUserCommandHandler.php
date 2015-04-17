<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\UserRepositoryInterface as UserRepository;
use Qdiscuss\Core\Events\UserWillBeDeleted;
use Qdiscuss\Core\Support\DispatchesEvents;

class DeleteUserCommandHandler
{
    use DispatchesEvents;

    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function handle($command)
    {
        $user = $command->user;
        $userToDelete = $this->users->findOrFail($command->userId, $user);

        $userToDelete->assertCan($user, 'delete');

        event(new UserWillBeDeleted($userToDelete, $command));

        $userToDelete->delete();
        $this->dispatchEventsFor($userToDelete);

        return $userToDelete;
    }
}
