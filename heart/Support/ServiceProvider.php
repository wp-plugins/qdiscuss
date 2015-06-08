<?php namespace Qdiscuss\Support;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Qdiscuss\Core\Models\Notification;
use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Post;
use Qdiscuss\Core\Models\Permission;
use Closure;

class ServiceProvider extends IlluminateServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	public function extend()
	{
		foreach (func_get_args() as $extender) {
			$extender->extend($this->app);
		}
	}
}
