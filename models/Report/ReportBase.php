<?php
defined('DIRECT_ACCESS_CHECK') or die;

/*
 *	Report_Base class
 *	
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.1
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Report
 */
abstract class Report_ReportBase extends Base implements Iterator {
	protected $report;
	protected $position;
	protected $order;
	protected $current;
	
	public function __construct() {
		$this->position = -1;
		$this->_init();
    }
	
	/**
	 *	Intialize the object.
	 *
	 *	@private
	 */
	private function _init() {
		$this->report = array();
	}
	
	/**
	 *	Re-intialize object
	 *
	 *	@public
	 */
	public function reset() {
		$this->_init();
	}
	
	/**
	 *	Current item via Iterator.
	 *
	 *	@public
	 *	@return Report The current report.
	 */
	public function current() {
		if ($this->position == -1) {
			$this->rewind();
			$this->next();
		}
		return $this->current;
	}
	
	/**
	 *	Current key name, part of Iterator object.
	 *
	 *	@public
	 *	@return string The key name.
	 */
	public function key() {
		if ($this->position == -1) {
			$this->rewind();
		}
		$key = $this->order[$this->position];
		return $key;
	}
	
	/**
	 *	Move to the next report.
	 *
	 *	Move and return it, part of Iterator object.
	 *
	 *	@public
	 *	@return Report
	 */
	public function next() {
		$key = $this->order[$this->position];
		$this->current = $this->report[$key];
		$this->position++;
		return $this->current;
	}
	
	/**
	 *	Reset the Iterator object to the first report.
	 *
	 *	@public
	 */
	public function rewind() {
		$this->order = array_keys($this->report);
		$this->position = 0;
	}
	
	/**
	 *	Is the current postion in the Iterator valid?
	 *
	 *	@public
	 *	@return Boolean
	 */
	public function valid() {
		if (($this->position > -1) && ($this->position < count($this->report))) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *	Get an 32-byte hash for a given data item.
	 *
	 *	@private
	 *	@param array()|string $data The data to get the item from
	 *	@param string $itemName The name of the item to use.  If non given then $data is a assumed to be a string, from which, a hash is generated.
	 *	@return string*32 The 32-byte string.
	 */
	protected function _get_hash($data,$itemName='') {
		if ($itemName != '') {
			return md5((string) $data[$itemName]);
		} else {
			return md5((string) $data);
		}
	}
	
	abstract public function parse($data);
}
?>