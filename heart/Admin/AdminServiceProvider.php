<?php namespace Qdiscuss\Admin;

use Illuminate\Support\ServiceProvider;
use Qdiscuss\Core\Support\AssetManager;

class AdminServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		// $root = __DIR__.'/../..';

		// $this->loadViewsFrom($root.'/views', 'qdiscuss.admin');

		// $this->publishes([
		// 	$root.'/public/fonts' => public_path('assets/fonts')
		// ]);

		// include __DIR__.'/routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['qdiscuss.admin.assetManager'] = $this->app->share(function ($app) {
			return new AssetManager($app['files'], $app['path.public'].'/web', 'admin');
		});
	}
}
