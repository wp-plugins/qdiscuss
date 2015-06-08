<?php Namespace Qdiscuss\Admin\Actions;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Qdiscuss\Api\Serializers\UserSerializer;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Support\Helper;
use Qdiscuss\Forum\Events\RenderView;
use Qdiscuss\Core\Support\Actor;

class IndexAction extends BaseAction
{
	use Helper;

	public function __construct(Actor $actor, UserRepositoryInterface $user)
	{
		$this->actor = $actor;
		$this->user = $user ;
	}

	public function get()
	{
		global $qdiscuss_endpoint, $qdiscuss_tittle, $qdiscuss_welcome_title, $qdiscuss_desc;

		if($user = $this->is_logined()){
		              $user = explode('|', $user);
		              $user_name = $user[0];
		              $user_info = User::where('username', $user_name)->first();

			$user = $this->user->findOrFail($user_info->id, $this->actor->getUser());
			 $serializer = new UserSerializer($this->actor, ['groups']);
			$document = $this->document()->setData($serializer->resource($user));
			$data = json_decode($this->respondWithDocument($document), true);

			$data_new = array();
			array_push($data_new, $data["data"], $data["included"][0]);
		                       
		              $data = $data_new;
		              $session = array('userId' => $user->id, 'token' => $_COOKIE['qdiscuss_remember']);
		} else {
		              $data = [];
		              $session = [];
		}

	              $config = array(
			'base_url' =>  get_site_url() . '/'. $qdiscuss_endpoint,
			'api_url' => get_site_url() . '/' . $qdiscuss_endpoint,
			'forum_title' => $qdiscuss_title,
			'welcome_title' => $qdiscuss_welcome_title,
			'welcome_description' => $qdiscuss_desc,
	              );

	              $assetManager = app('qdiscuss.admin.assetManager');
	              $root = __DIR__.'/../../..';
	              $assetManager->addFile([
	              	$root.'/front/js/admin/dist/app.js',
	              	$root.'/front/less/admin/app.less'
	              ]);

	              //event(new RenderView($view, $assetManager, $this));

	              $styles = array($assetManager->getCSSFiles());
	              $scripts =  array($assetManager->getJSFiles());

	             header("Content-Type: text/html; charset=utf-8");
	             echo include(__DIR__ . '/../Views/admin.php');
	             exit();
	}
	
}
