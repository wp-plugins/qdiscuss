<?php namespace Qdiscuss\Api;

use Illuminate\Support\ServiceProvider;
use Qdiscuss\Api\Serializers\BaseSerializer;

class ApiServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->singleton(
			'Illuminate\Contracts\Debug\ExceptionHandler',
			'Qdiscuss\Api\ExceptionHandler'
		);

		//include __DIR__.'/routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('Qdiscuss\Support\Actor');
	}
}
