<?php
if (!defined('DIRECT_ACCESS_CHECK')) {
	die('Direct access is not allowed');
}

/*
 *	Class for FREQ=MINUTELY
 *	
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.1
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Calendar
 */
class ICalRRuleParser_Minutely Extends ICalRRuleParser_Base implements ICalRRuleParser_Object {
    public function parse($rrule,$start) {
		
	}
}