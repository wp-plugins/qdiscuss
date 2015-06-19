<?php namespace Qdiscuss\Api\Serializers;

// use Qdiscuss\Core\Support\Helper;

class UserBasicSerializer extends BaseSerializer
{
    // use Helper;

    /**
     * The resource type.
     *
     * @var string
     */
    protected $type = 'users';

    /**
     * Serialize attributes of a User model for JSON output.
     *
     * @param User $user The User model to serialize.
     * @return array
     */
    protected function attributes($user)
    {

        $attributes = [
            'username'  => $user->display_name ? $user->display_name : $user->username,
            'avatarUrl'   => $user->avatar_url,
            // 'avatarUrl' => Helper::get_gravatar($user->email),
        ];

        return $this->extendAttributes($user, $attributes);
    }

    protected function groups()
    {
        return $this->hasMany('Qdiscuss\Api\Serializers\GroupSerializer');
    }
}
