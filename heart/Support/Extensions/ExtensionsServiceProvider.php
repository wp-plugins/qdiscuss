<?php namespace Qdiscuss\Support\Extensions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Capsule\Manager as DB;
use Qdiscuss\Core;

class ExtensionsServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		if (! Core::isInstalled() ){
			return ;
		}
		
		$extensions = json_decode(Core::config('extensions_enabled'), true);

		if($extensions){
			foreach ($extensions as $extension) {
				if (file_exists($file = qd_extensions_path() . '/'. $extension . '/bootstrap.php')) {
					require_once($file);
				}
			}
		}
	}
}