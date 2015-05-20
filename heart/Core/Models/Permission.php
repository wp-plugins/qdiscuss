<?php namespace Qdiscuss\Core\Models;

class Permission extends BaseModel
{
	protected static $permissions = [];

	public static function getPermissions()
	{
		return static::$permissions;
	}

	public static function addPermission($permission)
	{
		static::$permissions[] = $permission;
	}

}
