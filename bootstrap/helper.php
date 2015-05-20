<?php
/*
|--------------------------------------------------------------------------
| Register Helper Function
|--------------------------------------------------------------------------
|
*/
if ( ! function_exists('event')) {
	function event($command){
		global $qdiscuss_event;
		$qdiscuss_event->fire($command);
        	}
}

if( ! function_exists('app')){
	function app($class){
		global $qdiscuss_app;
		return $qdiscuss_app->make($class);
        	}
}

if( ! function_exists('qdiscuss_asset')){
	function qdiscuss_asset($avatar_path){
		return  wp_upload_dir()['baseurl'] . '/qdiscuss/avatars/' . $avatar_path;
        	}
}

if(! function_exists('base_path')){
	function base_path(){
		global $qdiscuss_app;
		return $qdiscuss_app['path'];
	}
}

if(! function_exists('qd_base_upload_path')){
	function qd_base_upload_path(){
		return rtrim(ABSPATH, '/') . DIRECTORY_SEPARATOR .  'wp-content' . DIRECTORY_SEPARATOR  . 'uploads';
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
 
if(! function_exists('extensions_path')){
	function extensions_path(){
		global $qdiscuss_app;
		return $qdiscuss_app['path'] . 'extensions';
	}
}