<?php Namespace Qdiscuss\Admin\Actions;

use Qdiscuss\Core\Models\User;
use Qdiscuss\Core\Models\Setting;
use Qdiscuss\Core\Repositories\EloquentUserRepository as UserRepositoryInterface;
use Qdiscuss\Api\Serializers\UserSerializer;
use Qdiscuss\Core\Actions\BaseAction;

class IndexAction extends BaseAction{

	public function __construct(UserRepositoryInterface $user)
	{
		$this->user = $user;
		global $qdiscuss_actor;
		$qdiscuss_actor->setUser($this->current_user());
		\Qdiscuss\Api\Serializers\BaseSerializer::setActor($qdiscuss_actor);
	}

	public function run()
	{
		global $qdiscuss_actor, $qdiscuss_endpoint, $qdiscuss_tittle, $qdiscuss_desc;

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
			'modulePrefix' => 'qdiscuss-admin',
			'environment' => 'production',
			'baseURL' =>  get_site_url() . '/'. $qdiscuss_endpoint,
			'apiURL' => get_site_url() . '/' . $qdiscuss_endpoint,
			'locationType' => 'hash',
			'EmberENV' => [],
			'APP' => [],
			'forumTitle' => $qdiscuss_title,
			'welcomeDescription' => $qdiscuss_desc,
	              );

	              $js_url = plugins_url('public/web/aforum.js', __DIR__.'/../../../../');
	              $css_url = plugins_url('public/web/aforum.css',  __DIR__.'/../../../../');

	             header("Content-Type: text/html; charset=utf-8");
	             echo include(__DIR__ . '/../Views/admin.php');
	             exit();
	}
	
}
