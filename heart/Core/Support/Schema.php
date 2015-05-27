<?php namespace Qdiscuss\Core\Support;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Database\Schema\Builder
 */
class Schema extends Facade {

	/**
	 * Get a schema builder instance for a connection.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public static function connection($name)
	{
		global $qdiscuss_app;
		return $qdiscuss_app['db']->connection($name)->getSchemaBuilder();
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		global $qdiscuss_app;
		return $qdiscuss_app['db']->connection()->getSchemaBuilder();
	}

}