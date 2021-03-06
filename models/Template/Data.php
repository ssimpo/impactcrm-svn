<?php
defined('DIRECT_ACCESS_CHECK') or die;

/**
 *	Class for <template:data />
 *	
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.1
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Template
 */
class Template_Data Extends Template_Base implements Template_Object {
	private $_standard_html_attributes = array(
		'style','class','rev','rel','href','src'
	);
    
	/**
	 *	Parse <template:data />
	 *
	 *	Include some data either from the supplied application settings,
	 *	supplied array, or supplied attributes.  Include against ACL.
	 *
	 *	@public
	 *	@param array $match text containing the attributes.
	 *	@return string The data results.
	 */
	public function parse($match) {
		$attributes = $match['attributes'];
		
		$template = '';
		if ($this->_show($attributes)) {
			if (array_key_exists('name',$attributes)) {
				$template = $this->_get_application_item($attributes['name']);
			} elseif (array_key_exists('opentag',$attributes)) {
				$htmlAttributes = '';
				foreach ($this->_standard_html_attributes as $attr) {
					if (array_key_exists($attr,$attributes)) {
						$htmlAttributes .= ' '.$attr.'="'.$attributes[$attr].'"';
					}
				}
				$template = '<'.$attributes['opentag'].$htmlAttributes.'>';
			} elseif (array_key_exists('closetag',$attributes)) {
				$template = '</'.$attributes['closetag'].'>';
			}
		}
		
		//Here you need to parse the content for more template data (allows for plugins...etc)
		if (array_key_exists('parsedata',$attributes)) {
			if ($attributes['parsedata'] = 'true') {
				$parser = new Template($this->application);
				$template = $parser->parse($template);
			}
		}

		return $template;
	}

}