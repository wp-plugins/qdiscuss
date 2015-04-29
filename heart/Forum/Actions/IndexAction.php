<?php Namespace Qdiscuss\Forum\Actions;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Repositories\UserRepositoryInterface;
use Qdiscuss\Core\Repositories\EloquentUserRepository;
use Qdiscuss\Api\Serializers\UserSerializer;
use Qdiscuss\Dashboard\Bridge;
use Qdiscuss\Core\Actions\BaseAction;
use Qdiscuss\Core\Models\Setting;

class IndexAction extends BaseAction{


	public function __construct()
	{
		global $qdiscuss_actor;
		$this->user = new EloquentUserRepository;
		$qdiscuss_actor->setUser($this->current_user());
                          \Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
	}

	public function get()
	{
		global $qdiscuss_actor, $qdiscuss_endpoint, $qdiscuss_tittle, $qdiscuss_welcome_title, $qdiscuss_desc;
		$qdiscuss_title = Setting::getForumTitle();
		$qdiscuss_welcome_title = Setting::getWelcomeTitle();
		$qdiscuss_desc = Setting::getForumDescription();

		if($user = $this->is_logined()){
			$user = explode('|', $user);
			$user_name = $user[0];
			$user_info = User::where('username', $user_name)->first();

		             $user = $this->user->findOrFail($user_info->id, $qdiscuss_actor->getUser());
		             $serializer = new UserSerializer(['groups']);
		             $document = $this->document()->setData($serializer->resource($user));
		             $data = json_decode($this->respondWithDocument($document), true);
		                
		             $data_new = array();
		             array_push($data_new, $data["data"], $data["included"][0]);
	             		$data = json_encode($data_new);
			$session = json_encode(array('userId' => $user->id, 'token' => $_COOKIE['qdiscuss_remember']));
		} else {
		    	$data = json_encode([]);
		    	$session = json_encode([]);
		}
		$config = array(
		        'modulePrefix' => 'qdiscuss',
		        'environment' => 'production',
		        'baseURL' =>  get_site_url() . '/' . $qdiscuss_endpoint,
		        'apiURL' => get_site_url() . '/' . $qdiscuss_endpoint,
		        'locationType' => 'hash',
		        'EmberENV' => [],
		        'APP' => [],
		        'forumTitle' => $qdiscuss_title,
		        'welcomeTitle' => $qdiscuss_welcome_title,
		        'welcomeDescription' => $qdiscuss_desc,
		);

		$js_url = plugins_url('public/web/cforum.min.js', __DIR__.'/../../../../');
		$css_url = plugins_url('public/web/cforum.css',  __DIR__.'/../../../../');
		
		header("Content-Type: text/html; charset=utf-8");
		echo include(__DIR__ . '/../Views/index.php');
		exit();
	}
	
}
