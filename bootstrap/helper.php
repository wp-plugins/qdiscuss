<?php
use Illuminate\Support\Str;
use Illuminate\Container\Container;
/*
|--------------------------------------------------------------------------
| Register Helper Function
|--------------------------------------------------------------------------
|
*/
if (! function_exists('app')) {
	/**
	  * Get the available container instance.
	  *
	  * @param  string  $make
	  * @param  array   $parameters
	  * @return  mixed|\QDiscuss\Application
	  */
	function app($make = null, $parameters = [])
	{
	        if (is_null($make)) {
		return Container::getInstance();
	        }
	        
	        return Container::getInstance()->make($make, $parameters);
	}
}

if (! function_exists('event')) {
	/**
	 * Fire an event and call the listeners.
	 *
	 * @param  string  $event
	 * @param  mixed   $payload
	 * @param  bool    $halt
	 * @return  array|null
	 */
	function event($event, $payload = array(), $halt = false)
	{
		return app('events')->fire($event, $payload, $halt);
	}
}

if( ! function_exists('qdiscuss_asset')){
	function qdiscuss_asset($avatar_path){
		return  wp_upload_dir()['baseurl'] . '/qdiscuss/avatars/' . $avatar_path;
        	}
}

if( ! function_exists('qdiscuss_attachment_path')){
	function qdiscuss_attachment_path($attachment_path){
		return  wp_upload_dir()['baseurl'] . '/qdiscuss/attachments/' . $attachment_path;
        	}
}

if(! function_exists('base_path')){
	function base_path(){
		return app('path');
	}
}

if(! function_exists('qd_base_wp_path')){
	function qd_base_wp_path(){
		return rtrim(ABSPATH, '/') . DIRECTORY_SEPARATOR .  'wp-content' . DIRECTORY_SEPARATOR  . 'qdiscuss';
	}
}

if(! function_exists('qd_upload_path')){
	function qd_upload_path(){
		return rtrim(ABSPATH, '/') . DIRECTORY_SEPARATOR .  'wp-content' . DIRECTORY_SEPARATOR  . 'uploads'  . DIRECTORY_SEPARATOR . 'qdiscuss';
	}
}

if(! function_exists('qd_storage_path')){
	function qd_storage_path(){
		return qd_upload_path() .  DIRECTORY_SEPARATOR . 'cache';
	}
}
 
if(! function_exists('qd_extensions_path')){
	function qd_extensions_path(){
		return qd_base_wp_path() . DIRECTORY_SEPARATOR . 'extensions';
	}
}

 
if(! function_exists('qd_language_path')){
	function qd_language_path(){
		return base_path() . 'public' . DIRECTORY_SEPARATOR . 'languages';
	}
}