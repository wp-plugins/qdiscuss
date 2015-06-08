<?php namespace Qdiscuss\Api\Actions\Discussions;

use Qdiscuss\Core\Commands\ReadDiscussionCommand;
use Illuminate\Contracts\Bus\Dispatcher;
use Qdiscuss\Core\Repositories\DiscussionRepositoryInterface;
use Qdiscuss\Core\Repositories\PostRepositoryInterface;
use Qdiscuss\Api\Actions\SerializeResourceAction;
use Qdiscuss\Api\Actions\Posts\GetsPosts;
use Qdiscuss\Api\JsonApiRequest;
use Qdiscuss\Api\JsonApiResponse;

class ShowAction extends SerializeResourceAction
{
	use GetsPosts;

	/**
	 * @var \Qdiscuss\Core\Repositories\DiscussionRepositoryInterface
	 */
	protected $discussions;

	/**
	 * @var \Qdiscuss\Core\Repositories\PostRepositoryInterface
	 */
	protected $posts;

	/**
	 * The name of the serializer class to output results with.
	 *
	 * @var string
	 */
	public static $serializer = 'Qdiscuss\Api\Serializers\DiscussionSerializer';

	/**
	 * The relationships that are available to be included, and which ones are
	 * included by default.
	 *
	 * @var array
	 */
	public static $include = [
		'startUser' => false,
		'lastUser' => false,
		'startPost' => true,
		'lastPost' => true,
		'posts' => true,
		'posts.user' => true,
		'posts.user.groups' => true,
		'posts.editUser' => true,
		'posts.hideUser' => true
	];

	/**
	 * The relations that are linked by default.
	 *
	 * @var array
	 */
	public static $link = ['posts'];

	/**
	 * The fields that are available to be sorted by.
	 *
	 * @var array
	 */
	public static $sortFields = ['time'];

	/**
	 * The default sort field and order to user.
	 *
	 * @var string
	 */
	public static $sort = ['time' => 'asc'];

	/**
	 * Instantiate the action.
	 *
	 * @param \Illuminate\Contracts\Bus\Dispatcher $bus
	 * @param \Qdiscuss\Core\Repositories\DiscussionRepositoryInterface $discussions
	 * @param \Qdiscuss\Core\Repositories\PostRepositoryInterface $posts
	 */
	public function __construct(Dispatcher $bus, DiscussionRepositoryInterface $discussions, PostRepositoryInterface $posts)
	{
		$this->bus = $bus;
		$this->discussions = $discussions;
		$this->posts = $posts;
	}

	/**
	 * Get a single discussion, ready to be serialized and assigned to the
	 * JsonApi response.
	 *
	 * @param \Qdiscuss\Api\JsonApiRequest $request
	 * @param \Qdiscuss\Api\JsonApiResponse $response
	 * @return \Qdiscuss\Core\Models\Discussion
	 */
	protected function data(JsonApiRequest $request, JsonApiResponse $response)
	{
		$user = $request->actor->getUser();

		$discussion = $this->discussions->findOrFail($request->get('id'), $user);

		$discussion->posts_ids = $discussion->posts()->whereCan($user, 'view')->get(['id'])->fetch('id')->all();

		if (in_array('posts', $request->include)) {
			$length = strlen($prefix = 'posts.');
			$relations = array_filter(array_map(function ($relationship) use ($prefix, $length) {
				return substr($relationship, 0, $length) === $prefix ? substr($relationship, $length) : false;
			}, $request->include));

			$discussion->posts = $this->getPosts($request, ['discussion_id' => $discussion->id])->load($relations);
		}

		$this->bus->dispatch(
		           new ReadDiscussionCommand($discussion->id, $user, 1)
		);

		return $discussion;
	}
}
