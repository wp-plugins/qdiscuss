<?php namespace Qdiscuss\Core\Handlers\Commands;

use Qdiscuss\Core\Repositories\UserRepositoryInterface as UserRepository;
use Qdiscuss\Core\Events\UserWillBeSaved;
use Qdiscuss\Core\Support\DispatchesEvents;

class EditUserCommandHandler
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
        $userToEdit = $this->users->findOrFail($command->userId, $user);

        $userToEdit->assertCan($user, 'edit');

        if (isset($command->username)) {
            $userToEdit->rename($command->username);
        }
        if (isset($command->email)) {
            $userToEdit->changeEmail($command->email);
        }
        if (isset($command->password)) {
            $userToEdit->changePassword($command->password);
        }
        if (isset($command->bio)) {
            $userToEdit->changeBio($command->bio);
        }
        if (! empty($command->readTime)) {
            $userToEdit->markAllAsRead();
        }
        if (! empty($command->preferences)) {
            foreach ($command->preferences as $k => $v) {
                $userToEdit->setPreference($k, $v);
            }
        }

        event(new UserWillBeSaved($userToEdit, $command));

        $userToEdit->save();
        $this->dispatchEventsFor($userToEdit);

        return $userToEdit;
    }
}
