<?php namespace Qdiscuss;

use Illuminate\Database\Capsule\Manager as DB; 
use Qdiscuss\Core\Support\Helper;

class Core
{
	use Helper;

	public static function isInstalled()
	{
		return self::table_exists('config');
	}

	public static function config($key, $default = null)
	{
		if (is_null($value = DB::table('config')->where('key', $key)->pluck('value'))) {
			$value = $default;
		}
		return $value;
	}

	/**
	 * Get the content of language file
	 * 
	 * @return string
	 */
	public static function getLanguage()
	{

		$language = Core::config('forum_language');
		if ($language && file_exists($file = qd_language_path() . '/' . $language . '.json')) {
			return file_get_contents($file);
		} else {
			return '';
		}
	}

}