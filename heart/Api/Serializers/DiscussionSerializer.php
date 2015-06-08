<?php namespace Qdiscuss\Api\Serializers;

class DiscussionSerializer extends DiscussionBasicSerializer
{
	/**
	 * Default relations to include.
	 *
	 * @var array
	 */
	protected $include = ['startUser', 'lastUser'];

	/**
	 * Serialize attributes of a Discussion model for JSON output.
	 *
	 * @param Discussion $discussion The Discussion model to serialize.
	 * @return array
	 */
	protected function attributes($discussion)
	{

		$attributes = parent::attributes($discussion);

		$user = $this->actor->getUser();
		$state = $discussion->stateFor($user);

		$attributes += [
			'commentsCount'  => (int) $discussion->comments_count,
			'startTime'      => $discussion->start_time->toRFC3339String(),
			'lastTime'       => $discussion->last_time ? $discussion->last_time->toRFC3339String() : null,
			'lastPostNumber' => $discussion->last_post_number,
			'canMove'  => $discussion->can($user, 'move'),// add neychang @todo to delete
			'canReply'       => $discussion->can($user, 'reply'),
			'canRename'      => $discussion->can($user, 'rename'),
			'canDelete'      => $discussion->can($user, 'delete'),
			'isSticky' =>  $discussion->is_sticky ? (bool) $discussion->is_sticky : false,// add neychang @todo to delete
			'canSticky' =>  (bool) $discussion->can($user, 'sticky'), // add neychang @todo to delete
			'readTime'       => $state && $state->read_time ? $state->read_time->toRFC3339String() : null,
			'readNumber'     => $state ? (int) $state->read_number : 0,
			'viewCounts'   => (int) $discussion->view_counts,
		];

		return $this->extendAttributes($discussion, $attributes);
	}
}
