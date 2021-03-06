<?php namespace Qdiscuss\Core\Models;

use Qdiscuss\Core\Support\Helper;

class AccessToken extends BaseModel
{
    use Helper;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'access_tokens';

    /**
     * Use a custom primary key for this model.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Generate an access token for the specified user.
     *
     * @param  int  $userId
     * @return static
     */
    public static function generate($userId)
    {
        $token = new static;

        // $token->id = str_random(40);
        $token->id = self::rand_str(40);
        $token->user_id = $userId;

        return $token;
    }

    /**
     * Define the relationship with the owner of this access token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Qdiscuss\Core\Models\User');
    }
}
