<?php namespace Qdiscuss\Extend;

use Qdiscuss\Core\Models\Permission as PermissionModel;
use Qdiscuss\Extend\SerializeAttributes;
use Qdiscuss\Core\Support\Helper;

class Permission implements ExtenderInterface
{
	protected $permission;
	protected $serialize = false;
	protected $grant = [];

	public function __construct($permission)
	{
		$this->permission = $permission;
	}

	public function serialize($serialize = true)
	{
		$this->serialize = $serialize;
		return $this;
	}

	public function grant($callback)
	{
		$this->grant[] = $callback;
		return $this;
	}

	public function extend()
	{
		global $qdiscuss_app, $qdiscuss_actor;
		$qdiscuss_actor->setUser(Helper::current_forum_user());// @todo to delete neychang
		PermissionModel::addPermission($this->permission);
		list($entity, $permission) = explode('.', $this->permission);
		if ($this->serialize) {
			$extender = new SerializeAttributes(
				'Qdiscuss\Api\Serializers\\'.ucfirst($entity).'Serializer',
				function (&$attributes, $model, $serializer) use ($permission, $qdiscuss_actor) {
					$attributes['can'.ucfirst($permission)] = (bool) $model->can($qdiscuss_actor->getUser(), $permission);
				}
			);
			$extender->extend();
		}
		foreach ($this->grant as $callback) {
			$model = 'Qdiscuss\Core\Models\\'.ucfirst($entity);
			$model::grantPermission($permission, $callback);
		}
	}
}