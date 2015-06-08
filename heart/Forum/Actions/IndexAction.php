<?php Namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Qdiscuss\Core\Repositories\EloquentUserRepository;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Support\Helper;
use Qdiscuss\Forum\Events\RenderView;
use Qdiscuss\Api\Request as ApiRequest;
use Qdiscuss\Core\Support\Actor;
use Qdiscuss\Core;
use Qdiscuss\Core\Support\LanguageManager;
use Qdiscuss\Forum\Events\LanguageSupport;

class IndexAction extends BaseAction
{
	use Helper;

	public function __construct(Actor $actor, EloquentUserRepository $user)
	{
		$this->user = $user;
		$this->actor = $actor;
	}

	public function get()
	{
		global $qdiscuss_app, $qdiscuss_endpoint, $qdiscuss_tittle, $qdiscuss_welcome_title, $qdiscuss_desc;
		$qdiscuss_title = Setting::getForumTitle();
		$qdiscuss_welcome_title = Setting::getWelcomeTitle();
		$qdiscuss_desc = Setting::getForumDescription();

		// if(($user = $this->actor->getUser()) && $user->exists) {
		if($user = self::is_logined()){
			$user = explode('|', $user);
			$user_name = $user[0];

			if($user_info = User::where('username', $user_name)->first()){
				$user = $this->user->findOrFail($user_info->id, $this->actor->getUser());//@todo
			}else{
				$user = self::register_user(get_user_by('login', $user_name));
				$this->actor->setUser($user);
			}

			$response = app('Qdiscuss\Api\Actions\Users\ShowAction')
				->handle(new ApiRequest(['id' => $user->id], $this->actor))
				->content->toArray();

			$data = [$response['data']];
			
			if (isset($response['included'])) {
				$data = array_merge($data, $response['included']);
			}

			$data = $data;
			$session = array('userId' => $user->id, 'token' => $_COOKIE['qdiscuss_remember']);
		} else {
		    	$data = [];
		    	$session = [];
		}

		$root = __DIR__.'/../../..';

		$config = array(
			'base_url' =>  get_site_url() . '/' . $qdiscuss_endpoint,
			'api_url' => get_site_url() . '/' . $qdiscuss_endpoint,
			'forum_title' => $qdiscuss_title,
			'welcome_title' => $qdiscuss_welcome_title,
			'welcome_message' => $qdiscuss_desc,
		);

		$languageManager = new LanguageManager;
		event(new LanguageSupport($languageManager));
		$language = $languageManager->getLanguageFilesConent();

		// var_dump($language);exit;

		$assetManager = app('qdiscuss.forum.assetManager');
		
		$assetManager->addFile([
			$root.'/front/js/forum/dist/app.js',
			$root.'/front/less/forum/app.less'
		]);

		// event(new RenderView($view, $assetManager, $this));
		event(new RenderView($data, $assetManager, $this->actor));

		$styles = array($assetManager->getCSSFiles());
		$scripts =  array($assetManager->getJSFiles());
		header("Content-Type: text/html; charset=utf-8");
		include(__DIR__ . '/../Views/index.php');
	}
	
}
