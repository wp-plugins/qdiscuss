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

		if (isset($command->data['username'])) {
			$userToEdit->rename($command->data['username']);
		}

		if (isset($command->data['email'])) {
			$userToEdit->changeEmail($command->data['email']);
		}

		if (isset($command->data['password'])) {
			$userToEdit->changePassword($command->data['password']);
		}

		if (isset($command->data['bio'])) {
			$userToEdit->changeBio($command->data['bio']);
		}

		if (! empty($command->data['readTime'])) {
			$userToEdit->markAllAsRead();
		}

		if (! empty($command->data['preferences'])) {
			foreach ($command->data['preferences'] as $k => $v) {
				$userToEdit->setPreference($k, $v);
			}
		}

		event(new UserWillBeSaved($userToEdit, $command));

		$userToEdit->save();
		$this->dispatchEventsFor($userToEdit);

		return $userToEdit;
	}
}
