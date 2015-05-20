<?php namespace Qdiscuss\Core\Notifications\Types;

interface AlertableNotification
{
	public function getAlertData();	
	public static function getType();

}