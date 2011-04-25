<?php
/*
 *	Class for testing against a geographic location using MaxMind's GeoIP
 *	database.
 *		
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.1
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Impact
 */
class Acl_AGENT extends Acl_TestBase implements Acl_Test {
	private $lookup = array();
	private $agent = null;
	
	/**
         *	Constructor.
         *
         *	@public
         *	@param object $application The current (or other) application object.
         *	@return object Acl_AGENT
         */
	public function __construct($application=null) {
		if (!defined('DS')) {
			define('DS',DIRECTORY_SEPARATOR);
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->agent = $_SERVER['HTTP_USER_AGENT'];
		}
	}
	
	/**
	 *	Get browser information.
	 *
	 *	Will use get_browser if available, otherwise will use the PEAR
	 *	equivalent, which is slower but available to more people.
	 *
	 *	@private
	 *	@param string $agent The browser user-agent string.
	 *	@return object Returner from get_growser.
	 */
	private function _get_browser_info($agent) {
		if (!isset($this->lookup[$agent])) {
			try {
				$this->lookup[$agent] = get_browser($agent);
			} catch (Exception $e) {
				require_once ROOT_BACK.'includes'.DS.'browscap'.DS.'Browscap.php';
				$browscap = new Browscap(ROOT_BACK.'database'.DS);
				$this->lookup[$agent] = $browscap->getBrowser($agent);
			}
		}
		return $this->lookup[$agent];
	}
	
	/**
	 *	Is the user, using a specified browser?
	 *
	 *	@protected
	 *	@param array $attributes The browser name to test against (expected $attributes[0] = '<BROWSER>').
	 *	@return boolean
	 */
	public function test_browser($attributes) {
		$browscap = $this->_get_browser_info($this->agent);
		return (strtoupper($browscap->browser) == strtoupper($attributes[0]));
	}
	
	/**
	 *	Is the user, using a specified platform?
	 *
	 *	@protected
	 *	@param array $attributes The platform name to test against (expected $attributes[0] = '<PLATFROM>').
	 *	@return boolean
	 */
	public function test_platform($attributes) {
		$browscap = $this->_get_browser_info($this->agent);
		return (strtoupper($browscap->platform) == strtoupper($attributes[0]));
	}
	
	/**
	 *	Is the user, on a mobile platform?
	 *
	 *	@protected
	 *	@param array $attributes This is expected to be blank
	 *	@return boolean
	 */
	public function test_mobile($attributes) {
		if ($this->_is_using_mobile_subdomain()) {
			return true;
		}
		if ($this->_media_is_set_to_mobile()) {
			return true;
		}
		
		$browscap = $this->_get_browser_info($this->agent);
		return ($browscap->ismobiledevice == 1);
	}
	
	/**
	 *	Has the user asked for a mobile version via the query-string.
	 *
	 *	@private
	 *	@return boolean.
	 */
	private function _media_is_set_to_mobile() {
		if (isset($_GET['media'])) {
			if (strtoupper(addslashes($_GET['media'])) == 'MOBILE') {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Is the user accessing the content via specified mobile subdomain?
	 *
	 *	@private
	 *	@return boolean
	 */
	private function _is_using_mobile_subdomain() {
		if ((defined('DOMAIN')) && (defined('MOBILE_SUBDOMAIN'))) {
			$parts = explode('.',DOMAIN);
			if (substr(DOMAIN,0,strlen($parts[0])+1) == $parts[0]+'.') {
				return true;
			}
		}
		return false;
	}
}
?>