<?php namespace Qdiscuss;

/** Include the bootstrap for setting up WordPress environment */
// include( ABSPATH . '/wp-load.php' );

class Toro
{
    public static function serve($routes)
    {
        ToroHook::fire('before_request', compact('routes'));
        // var_dump($_SERVER);exit;
        $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        $path_info = '/';
        $site_url = get_site_url();
        // echo "http://" . $_SERVER['SERVER_NAME'];exit;
        $sub_site = '';
        if($site_url != "http://" . $_SERVER['HTTP_HOST']){
	$url_string = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
              $url_array = parse_url($url_string);
              // var_dump($url_array);exit;
              $path = explode('/', $url_array['path']);
              $sub_site = $path[1];
        }

        

        // echo $url;exit;
         // var_dump($_SERVER);exit;
        // var_dump($path);exit;
        // if (empty( $GLOBALS['wp']->query_vars['json_route'])){

        //     return;
        // }
// echo 'ss';exit;
// var_dump($GLOBALS['wp']);exit;
        // echo $GLOBALS['wp']->query_vars['json_route'] ;exit;
    // var_dump(get_site_url());exit;
    //  var_dump($_SERVER);exit;
            if (! empty($_SERVER['PATH_INFO'])) {
                $path_info = $_SERVER['PATH_INFO'];
           
            } elseif (! empty($_SERVER['ORIG_PATH_INFO']) && $_SERVER['ORIG_PATH_INFO'] !== '/index.php') {
                $path_info = $_SERVER['ORIG_PATH_INFO'];
                // echo 'sf';
            }else {
                if (! empty($_SERVER['REQUEST_URI'])) {
                    $path_info = (strpos($_SERVER['REQUEST_URI'], '?') > 0) ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
                    // echo $path_info;
                    // echo 'qq';
                }
            }
            // echo $sub_site;exit;
        if($sub_site){
        	$path_info = str_replace('/' . $sub_site, '', $path_info);

        	// echo $path_info;exit;
        }
        // echo $path_info;exit;
        $discovered_handler = null;
        $regex_matches = array();

        if (isset($routes[$path_info])) {
            $discovered_handler = $routes[$path_info];
        } elseif ($routes) {
            $tokens = array(
                ':string' => '([a-zA-Z]+)',
                ':number' => '([0-9]+)',
                ':alpha'  => '([a-zA-Z0-9-_]+)'
            );
            foreach ($routes as $pattern => $handler_name) {
                $pattern = strtr($pattern, $tokens);
                if (preg_match('#^/?' . $pattern . '/?$#', $path_info, $matches)) {
                    $discovered_handler = $handler_name;
                    $regex_matches = $matches;
                    break;
                }
            }
        }

        $result = null;
        $handler_instance = null;

        if ($discovered_handler) {
            if (is_string($discovered_handler)) {
                $handler_instance = new $discovered_handler();
            } elseif (is_callable($discovered_handler)) {
                $handler_instance = $discovered_handler();
            }
        }

        if ($handler_instance) {
            unset($regex_matches[0]);

            if (self::is_xhr_request() && method_exists($handler_instance, $request_method . '_xhr')) {
                header('Content-type: application/json');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header('Pragma: no-cache');
                $request_method .= '_xhr';
            }

            if (method_exists($handler_instance, $request_method)) {

                ToroHook::fire('before_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
                $result = call_user_func_array(array($handler_instance, $request_method), $regex_matches);
                ToroHook::fire('after_handler', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
            } else {
                ToroHook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
            }
        } else {
            // ToroHook::fire('404', compact('routes', 'discovered_handler', 'request_method', 'regex_matches'));
        }
        // echo 'ss';exit;
        // ToroHook::fire('after_request', compact('routes', 'discovered_handler', 'request_method', 'regex_matches', 'result'));
          // echo 'ss';exit;
    }

    private static function is_xhr_request()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
}

class ToroHook
{
    private static $instance;

    private $hooks = array();

    private function __construct() {}
    private function __clone() {}

    public static function add($hook_name, $fn)
    {
        $instance = self::get_instance();
        $instance->hooks[$hook_name][] = $fn;
    }

    public static function fire($hook_name, $params = null)
    {
        $instance = self::get_instance();
        if (isset($instance->hooks[$hook_name])) {
            foreach ($instance->hooks[$hook_name] as $fn) {
                call_user_func_array($fn, array(&$params));
            }
        }
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ToroHook();
        }
        return self::$instance;
    }
}

// ToroHook::add("404",  function() {echo 'sorry nothing find for you! : (';exit;});