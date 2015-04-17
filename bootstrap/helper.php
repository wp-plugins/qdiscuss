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
        