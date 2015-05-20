<?php namespace Qdiscuss\Support\Extensions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Capsule\Manager as DB; 

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
		$extensions = json_decode(DB::table('config')->where('key', 'extensions_enabled')->pluck('value'), true);

		foreach ($extensions as $extension) {
			if ($extension['is_activated'] == 1 && file_exists($file = extensions_path().'/'.$extension.'/bootstrap.php')) {
				require $file;
			}
		}
	}
}