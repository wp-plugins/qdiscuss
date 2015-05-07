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
     *  Check whether the user has the auth by token
     *  
     * @return boolean 
     */
    protected function is_auth()
    {
        return true;
    }

}
