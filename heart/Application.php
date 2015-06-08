<?php namespace Qdiscuss;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Qdiscuss\Support\Config\Repository as ConfigRepository;
use Qdiscuss\Support\Filesystem\Filesystem;
use Qdiscuss\Support\Filesystem\FilesystemManager;
use Qdiscuss\Core\Support\Actor;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\QueueEntityResolver;
use Illuminate\Database\Connectors\ConnectionFactory;
use Slim\Slim;

class  Application extends Container implements  ApplicationContract 
{

	/**
	  * The configuration path of the application installation.
	  *
	  * @var string
	  */
	protected $configPath;

	/**
	  * The loaded service providers.
	  *
	  * @var array
	  */
	protected $loadedProviders = [];

	/**
	  * The loaded config.
	  *
	  * @var array
	  */
	protected $loadedConfigurations = [];

	/**
	  * The service binding methods that have been executed.
	  *
	  * @var array
	  */
	protected $ranServiceBinders = [];

	/**
	  * Create a new QDiscuss application instance.
	  *
	  * @param  string|null  $basePath
	  * @return   void
	  */
	public function __construct($basePath = null)
	{
	        // date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
	        $this->basePath = $basePath;
	        $this->bootstrapContainer();
	        // $this->registerErrorHandling();
	}

	/**
	  * Bootstrap the application container.
	  *
	  * @return void
	  */
	protected function bootstrapContainer()
	{
	        static::setInstance($this);
	        $this->instance('app', $this);
	        $this->registerContainerAliases();
	}

	/**
	 * Get the version number of the application.
	 *
	 * @return string
	 */
	public function version()
	{
		return 'QDiscuss Core (0.0.1)';
	}

	/**
	 * Get the base path for the application.
	 *
	 * @param  string  $path
	 * @return   string
	 */
	public function basePath($path = null)
	{

		if (isset($this->basePath)) {
			return $this->basePath.($path ? '/'.$path : $path);
		}

		if ($this->runningInConsole() || php_sapi_name() === 'cli-server') {
			$this->basePath = getcwd();
		} else {
			$this->basePath = realpath(getcwd().'/../');
		}

		return $this->basePath($path);

	}

	/**
	 * Get or check the current application environment.
	 *
	 * @param  mixed
	 * @return   string
	 */
	public function environment()
	{
		return 'production';
	}

	/**
	 * Determine if the application is currently down for maintenance.
	 *
	 * @return bool
	 */
	public function isDownForMaintenance()
	{
		return false;
	}

	/**
	 * Register all of the configured providers.
	 *
	 * @return void
	 */
	public function registerConfiguredProviders()
	{
		//
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  \Illuminate\Support\ServiceProvider|string  $provider
	 * @param  array  $options
	 * @param  bool   $force
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function register($provider, $options = array(), $force = false)
	{
		if (!$provider instanceof ServiceProvider) {
		            $provider = new $provider($this);
		}
		
		if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
		            return;
		}
		
		$this->loadedProviders[$providerName] = true;
		$provider->register();
		$provider->boot();

	}

	/**
	 * Register a deferred provider and service.
	 *
	 * @param  string  $provider
	 * @param  string  $service
	 * @return   void
	 */
	public function registerDeferredProvider($provider, $service = null)
	{
		return $this->register($provider);
	}

	/**
	 * Boot the application's service providers.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register a new boot listener.
	 *
	 * @param  mixed  $callback
	 * @return void
	 */
	public function booting($callback)
	{

	}

	/**
	 * Register a new "booted" listener.
	 *
	 * @param  mixed  $callback
	 * @return void
	 */
	public function booted($callback)
	{

	}

	/**
	  * Resolve the given type from the container.
	  *
	  * @param  string  $abstract
	  * @param  array   $parameters
	  * @return mixed
	  */
	public function make($abstract, $parameters = [])
	{
		if (array_key_exists($abstract, $this->availableBindings) &&
		    ! array_key_exists($this->availableBindings[$abstract], $this->ranServiceBinders)) {
			$this->{$method = $this->availableBindings[$abstract]}();
			$this->ranServiceBinders[$method] = true;
		}

		return parent::make($abstract, $parameters);

	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	protected function registerBusBindings()
	{
		$this->singleton('bus', function () {
		// $this->singleton('Illuminate\Contracts\Bus\Dispatcher', function () {
	            		$this->register('Illuminate\Bus\BusServiceProvider');
	            		return $this->make('Illuminate\Contracts\Bus\Dispatcher');
	        	});
	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	public function registerConfigBindings()
	{
		$this->singleton('config', function () {
			return new ConfigRepository;
		});
	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	protected function registerDatabaseBindings()
	{
		//@ todo
		// $this->singleton('db', function () {
		// 	return $this->loadComponent(
		// 		'database', [
		//             			'Illuminate\Database\DatabaseServiceProvider',
		//             			'Illuminate\Pagination\PaginationServiceProvider'],//@todos
		//         		'db'
		//     	);
		// });
		global $qdiscuss_config;
		$this['config']->set("database.connections", $qdiscuss_config['database']);
		$this->singleton('Illuminate\Contracts\Queue\EntityResolver', function()
		{
			return new QueueEntityResolver;
		});
		$this->singleton('db.factory', function($this)
		{
			return new ConnectionFactory($this);
		});
		$this->singleton('db', function($this)
		{
			return new DatabaseManager($this, $this['db.factory']);
		});
	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	protected function registerEventBindings()
	{
		$this->singleton('events', function () {
	            		$this->register('Illuminate\Events\EventServiceProvider');
	            		return $this->make('events');
	        	});
	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	protected function registerFilesBindings()
	{
		$this->singleton('files', function () {
	            		return new Filesystem;
	        	});
	        	
	        	//@todo
	        	// $this->singleton('filesystem', function () {
	        	// 	$this['config']->set("filesystems.default", "local");
	        	// 	$this['config']->set("filesystems.cloud", "s3");
	        	// 	$this['config']->set("filesystems.disks.qdiscuss-avatars", array("driver" => "local", "root" => qd_upload_path() . DIRECTORY_SEPARATOR . 'avatars'));
	         //        	return $this->loadComponent('filesystems', 'Qdiscuss\Filesystem\FilesystemServiceProvider', 'filesystem');
	        	// });

        		$this->singleton('filesystem', function() {
        	  		$this['config']->set("filesystems.default", "local");
        	  		$this['config']->set("filesystems.cloud", "s3");
        	  		$this['config']->set("filesystems.disks.qdiscuss-avatars", array("driver" => "local", "root" => qd_upload_path() . DIRECTORY_SEPARATOR . 'avatars'));
        			$this['config']->set("filesystems.disks.qdiscuss-attachments", array("driver" => "local", "root" => qd_upload_path() . DIRECTORY_SEPARATOR . 'attachments'));
        			return new FilesystemManager($this);
        		});

        		// $this->singleton('filesystem.disk', function() {
        		// 	return $this['filesystem']->disk("qdiscuss-avatars")->getDriver();
        		// });
        		$this->bind('filesystem.disk.avatars', function() {
        			return $this['filesystem']->disk("qdiscuss-avatars")->getDriver();
        		});
        		$this->bind('filesystem.disk.attachments', function() {
        			return $this['filesystem']->disk("qdiscuss-attachments")->getDriver();
        		});
	}

	protected function registerRouterBindings()
	{
		$this->singleton('router', function(){
			return new Slim;
		});
	}

	/**
	  * Register container bindings for the application.
	  *
	  * @return void
	  */
	protected function registerMailBindings()
	{
		$this->singleton('mailer', function () {
	            		$this->configure('services');
	            		return $this->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
	        	});
	}

	/**
	  * Configure and load the given component and provider.
	  *
	  * @param  string  $config
	  * @param  array|string  $providers
	  * @param  string|null  $return
	  * @return  mixed
	  */
	protected function loadComponent($config, $providers, $return = null)
	{
		$this->configure($config);

		foreach ((array) $providers as $provider) {
	           		$this->register($provider);
	       	}
	       
	       	return $this->make($return ?: $config);
	}

	/**
	  * Load a configuration file into the application.
	  *
	  * @return void
	  */
	public function configure($name)
	{
		if (isset($this->loadedConfigurations[$name])) {
	           		return;
	       	}
	
		$this->loadedConfigurations[$name] = true;
		$path = $this->getConfigurationPath($name);
	       
		if ($path) {
			$this->make('config')->set($name, require $path);
		}
	}


	/**
	  * Get the path to the given configuration file.
	  *
	  * @param  string  $name
	  * @return string
	  */
	protected function getConfigurationPath($name)
	{
		$appConfigPath = ($this->configPath ?: $this->basePath('config')).'/'.$name.'.php';

		if (file_exists($appConfigPath)) {
	           		return $appConfigPath;
	       	} elseif (file_exists($path = __DIR__.'/../config/'.$name.'.php')) {
	           		return $path;
		}

	}

	/**
	  * Determine if the application is running in the console.
	  *
	  * @return string
	  */
	public function runningInConsole()
	{
		return php_sapi_name() == 'cli';
	}

	/**
	  * Register the core container aliases.
	  *
	  * @return void
	  */
	protected function registerContainerAliases()
	{
		$this->aliases = [
			'Illuminate\Contracts\Foundation\Application' => 'app',
			'Illuminate\Container\Container' => 'app',
			'Illuminate\Contracts\Container\Container' => 'app',
			'Illuminate\Contracts\Mail\Mailer' => 'mailer',
			'Illuminate\Contracts\Queue\Queue' => 'queue.connection',
		    	'Qdiscuss\Support\Filesystem\Filesystem' => 'filesystem',
		];
	}

	/**
	  * The available container bindings and their respective load methods.
	  *
	  * @var array
	  */
	public $availableBindings = [
		'bus'     => 'registerBusBindings',
		'Illuminate\Contracts\Bus\Dispatcher' => 'registerBusBindings',
		'config' => 'registerConfigBindings',
		'db' => 'registerDatabaseBindings',
		'events' => 'registerEventBindings',
		'Illuminate\Contracts\Events\Dispatcher' => 'registerEventBindings',
		'files' => 'registerFilesBindings',
		'filesystem' => 'registerFilesBindings',
		'filesystem.disk.avatars' => 'registerFilesBindings',
		'filesystem.disk.attachments' => 'registerFilesBindings',
		'Illuminate\Contracts\Filesystem\Factory' => 'registerFilesBindings',
		'router' => 'registerRouterBindings',
	];

}
