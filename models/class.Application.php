<?php
/**
 *	Main impact class
 *	
 *	Class containing a vast amount of functions and different concepts.
 *	Will need breaking down into seperate classes for different aspects of the
 *	platform. Eg. Seperate Database and Facebook classes?
 *		
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.4
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 
 *	@todo Breakdown into seperate classes for different areas
 *	@package Impact
 *	@extends Impact_Base
 */
class Application Extends Impact_Base {
	private static $instance;
	public $settings = array();
	public $Acl;
	public $facebook;
	public $fbsession;
	public $me;
	
	/**
	 *	Main constructor.
	 *
	 *	@private
	 *	@deprecated
	 */
	private function __construct() {
	}
	
	/**
	 *	Singleton method.
	 *
	 *	Provide a reference to the one static instance of this class.  Stops
	 *	class being declared muliple times.
	 *
	 *	@public
	 *	@static
	 *
	 *	@return Impact
	 *	
	 */
	public static function singleton() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	/**
	 *	Intitization method.
	 *
	 *	@public
	 */
	public function setup() {
		$this->settings['FBID'] = 0;
		$this->_load_constants();
		$this->_make_facebook_connection();
		$this->_language_detect();
		$this->_media_detect();
		$this->_user_access_detect();
		
		$this->pageName = strtolower(addslashes($_GET['page']));
		if ($this->pageName == '') {
			$this->pageName = DEFAULT_HOMEPAGE;
		}
		$this->pageErrorCheck = $this->_get_page_request_info();
	}
	
	/**
	 *	Load constants from an XML file.
	 *
	 *	Loads a series of constants from a settings file (XML).  Values
	 *	are loaded into the global scope.
	 *
	 *	@private.
	 *	@todo Make generic so that settings can be loaded from anywhere?
	 */
	private function _load_constants() {
		I::load_config(I::get_include_directory().'/../config/settings.xml');
	}
	
	/**
	 *	Make a Facebook connection.
	 *
	 *	Connect to Facebook and return the session and Facebook objects
	 *	to Impact properties (Facebook and fbsession).
	 *
	 *	@public
	 *	@todo Update to the newest Facebook API.
	 */
	function _make_facebook_connection() {
		require_once(ROOT_BACK.'/includes/facebook.php');
		
		$this->facebook = new Facebook(array(
			'appId'  => FB_APPKEY,
			'secret' => FB_SECRET,
			'cookie' => true,
		));
		$this->fbsession = $this->facebook->getSession();
	
		if ($this->fbsession) {
			try {
				$this->uid = $this->facebook->getUser();
				$this->me = $this->facebook->api('/me');
			} catch (FacebookApiException $e) {
				error_log($e);
			}
		}
	}
	
	/**
	 *	Cloning method.
	 *
	 *	This returns an error, since cloning of a singleton is not allowed.
	 *
	 *	@public
	 */
	public function __clone() {
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	/**
	 *	Generic set property method.
	 *
	 *	Set the value of an application property.  Values are stored in
	 *	the application array and accessed via the __set and __get methods.
	 *
	 *	@public
	 */
	public function __set($property,$value) {
		$this->settings[$property] = $value;
	}
	
	/**
	 *	Generic get property method.
	 *
	 *	Get the value of an application property.  Values are stored in
	 *	the application array and accessed via the __set and __get methods.
	 *
	 *	@publi
	 */
	public function __get($property) {
		//$convertedProperty = I::function_to_variable($property);
		if (array_key_exists($property,$this->settings)) {
			return $this->settings[$property];
		} else {
			return false;
		}
	}
	
	/**
	 *	Set the current users access levels.
	 *
	 *	These are calculated from data stored in the database.
	 *
	 *	@private
	 *	@todo Needs a bit of work to improve it but works well and dosen't have any major security flaws.
	 */
	private function _user_access_detect() {
		$database = Database::singleton();
		$this->roles = $database->get_roles($this->FBID);
		$this->accessLevel = $database->get_access($this->FBID);
		
		//This needs a better implimentation but will do to get us going
		$this->Acl = $this->factory('Acl');
		$this->Acl->FBID = $this->settings['FBID'];
		$this->Acl->accesslevel = $this->settings['accessLevel'];
		$this->Acl->facebook = $this->facebook;
		$this->Acl->load_roles($this->settings['roles']);
		$this->settings['Acl'] = $this->Acl;
	}
	
	/**
	 *	Check if current page request is valid.
	 *
	 *	Does the requested page exist? Does the current user have access.
	 *
	 *	@public
	 *	@return boolean Is the request valid.
	 */
	function _get_page_request_info() {
		$this->entityID = 0;
		$errorcheck = false;
		$database = Database::singleton();
		
		$reader_roles = $database->create_roles_sql('readers');
	
		if (is_numeric($this->pageName)) {
			$errorcheck = $database->get_row(
				DEFAULT_CACHE_TIMEOUT,
				'SELECT Title FROM entities WHERE (ID='.$this->pageName.') AND '.$reader_roles
			);
			if ($errorcheck) {
				$this->entityID = $application['pageName'];
				$this->pageName = $errorcheck['Title'];
			} 
		} else {
			$errorcheck = $database->get_row(
				DEFAULT_CACHE_TIMEOUT,
				'SELECT ID FROM entities WHERE (Title="'.$this->pageName.'") AND '.$reader_roles
			);
			if ($errorcheck) {
				$this->entityID = $errorcheck['ID'];
			} 
		}
	
		return ($errorcheck)?true:false;
	}
	
	/**
	 *	Detect the media being used.
	 *
	 *	Will detect the media being used (eg. Desktop PC, Mobile, iPad,
	 *	Facebook, ...etc). Data is returned to the media property.
	 *	
	 *	@protected
	 *	@todo Add detection for wider range of media.
	 */
	protected function _media_detect() {
		$media = DEFAULT_MEDIA;
		if (isset($_GET['media'])) {
			$media = strtoupper(addslashes($_GET['media']));
		} else {
			//Auto-detection of Robots and FB needed :)
			if (substr(DOMAIN,0,2) == 'm.') { //Accessed the mobile subdomain
				$media = 'MOBILE';
			} elseif ($application['browser']->isMobileDevice) {
				$media = 'MOBILE';
			}
		}
		
		$this->media = I::reformat_role_string($media);
	}
	
	/**
	 *	Dectect the language being used.
	 *
	 *	What is the native language of the current user?  Is detected from
	 *	the browser request headers and query-string.  Data is returned to
	 *	the language property.
	 *
	 *	@protected
	 *	@todo Allow Facebook language detection and user database lookup (for stored setting).
	 */
	protected function _language_detect() {
		$lang = DEFAULT_LANG;
		if (isset($_GET['lang'])) {
			$lang = strtolower(addslashes($_GET['lang']));
		} else {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { //Auto detection of first language
				$lang = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
				$lang = $lang[0];
				$lang = str_replace('-','_',$lang);
			}
		}
		
		$this->language = I::reformat_role_string($lang);
	}
}
?>