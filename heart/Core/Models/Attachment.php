<?php namespace Qdiscuss\Core\Models;

use Tobscure\Permissible\Permissible;
use Qdiscuss\Core\Support\EventGenerator;
// use Qdiscuss\Core\Events\attachmentWasDeleted;
// use Qdiscuss\Core\Events\attachmentWasStarted;
// use Qdiscuss\Core\Events\attachmentWasRenamed;
use Qdiscuss\Core\Models\User;

class Attachment extends BaseModel
{
	use Permissible;

	/**
	 * Disable timestamps.
	 *
	 * @var boolean
	 */
	public $timestamps = true;

	/**
	 * The validation rules for this model.
	 *
	 * @var array
	 */
	public static $rules = [
		'user_id'    => 'integer',
		'path'        => 'required'
	];

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'attachments';

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at'];

	/**
	 * The user for which the state relationship should be loaded.
	 *
	 * @var \Qdiscuss\Core\Models\User
	 */
	protected static $uploader;

	/**
	 * Raise an event when a attachment is deleted.
	 *
	 * @return void
	 */
	public static function boot()
	{
		parent::boot();

		static::deleted(function ($attachment) {
			$attachment->raise(new attachmentWasDeleted($attachment));
		});
	}

	/**
	 * Create a new instance.
	 *
	 * @param  integer $userId
	 * @param  string  $path
	 * @return static
	 */
	public static function start($userId, $path)
	{
		$attachment = new static;

		$attachment->user_id = $userId;
		$attachment->path = $path;

		// $attachment->raise(new attachmentWasStarted($attachment));

		return $attachment;
	}

	/**
	 * Define the relationship with the attachment's uploaders.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function uploader()
	{
		return $this->belongsTo('Qdiscuss\Core\Models\User');
	}

	public function getPathAttribute()
	{
		$path = $this->attributes['path'];
		return $path ? qdiscuss_attachment_path($path) : null;
	}

}
