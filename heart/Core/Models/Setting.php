<?php namespace Qdiscuss\Core\Models;

use Illuminate\Database\Capsule\Manager as DB; 


class Setting extends BaseModel
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'config';

	/**
	 * Use a custom primary key for this model.
	 *
	 * @var boolean
	 */
	public $incrementing = false;

	/**
	 *  Get the forum title setting value
	 *  
	 * @return string
	 */
	public static function getForumTitle()
	{
		$forum_title =self::getValueByKey('forum_title');
		
		if(!$forum_title) return 'QDiscuss';
		
		return $forum_title;
	}

	/**
	 *  Get the welcome title setting value 
	 *  
	 * @return string
	 */
	public static function getWelcomeTitle()
	{
		$forum_welcome_title  = self::getValueByKey('forum_welcome_title');

		if (!$forum_welcome_title) return 'Welcome To QDiscuss';

		return $forum_welcome_title;
	}

	/**
	 *  Get the forum description setting value 
	 *  
	 * @return string
	 */
	public static function getForumDescription()
	{
		$forum_desc  = self::getValueByKey('forum_description');

		if (!$forum_desc) return 'An Amazing Forum Plugin Base On WordPress By <a href="http://colorvila.com">ColorVila</a>';

		return $forum_desc;
	}
  
  	/**
  	 * Get the forum endpoint
  	 * 
  	 * @return string
  	 */
	public static function getEndPoint()
	{
		$forum_endpoint = self::getValueByKey('forum_endpoint');

		if(!$forum_endpoint) return 'qdiscuss';

		return $forum_endpoint;
	}

	/**
	 *  Get the value by config key
	 *  
	 * @param  string $key
	 * @return   string $value
	 */
  	public static function getValueByKey($key)
  	{
  		return Setting::where('key', $key)->lists('value')[0];
  	}

  	/**
  	* Set key value pair
  	*  
  	* @param string $key
  	* @param string $value
  	* @return  void
  	*/
  	public function setValue($key, $value)
  	{
  		DB::table('config')
  			->where('key', $key)
  			->update(array(
  				"value" => $value,
  			));
  	}

}
