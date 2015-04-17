<?php namespace Qdiscuss\Core\Support;

use Illuminate\Contracts\Bus\Dispatcher;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Wpuser;
use Qdiscuss\Core\Models\Guest;

abstract class Action
{
    abstract public function handle();

    public function __construct(Actor $actor, Dispatcher $bus)
    {
        $this->actor = $actor;
        $this->bus = $bus;
        
    }

    protected function callAction($class, $params = [])
    {
        global $qdiscuss_app;
        $action = $qdiscuss_app->make($class);
        return $action->call($params);
    }

    /**
      * Check whether the user is logined or not
      *
      * @return boolean
      */
    protected function is_logined()
    {
        if (count($_COOKIE)) {
            foreach ($_COOKIE as $key => $val) {
                        if (substr($key, 0, 19) === "wordpress_logged_in") {
                            return $val;
                        }
            }
        }

        return false;

    }

    /**
     *  Check whether the user has the auth by token
     *  
     * @return boolean 
     */
    protected function is_auth()
    {
        return true;
    }

    /**
     * Get the current user instance
     *
     * @return [type] [description]
     */
    protected function current_user()
    {
        if($user = $this->is_logined()){
            $user = explode('|', $user);
            $user_name = $user[0];
            return User::where('username', $user_name)->first();
        }

        return new Guest;
    }

}
