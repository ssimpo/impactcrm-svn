<?php
defined('DIRECT_ACCESS_CHECK') or die;

/**
 *	Calendar.Event class
 *		
 *	@author Stephen Simpson <me@simpo.org>
 *	@version 0.0.3
 *	@license http://www.gnu.org/licenses/lgpl.html LGPL
 *	@package Calendar	
 */
class DateParser_Iso8601date implements DateParser_Object {
	
	/**
	 *	Date parser method.
	 *
	 *	@param string $date The date to parse.
	 *	@param string $timezone The timezone of the date.
	 *	@return date The date in standard PHP date format.
	 */
	public function parse($date,$timezone='') {
		$dateLen = strlen($date);
		$datetime = new Calendar_DateTime();
		
		$hour=$minute=$second=0;$year=$day=$month='';
		if (($dateLen == 8) || ($dateLen == 15) || ($dateLen == 16)) {
			$datetime->year = substr($date,0,4);
			$datetime->month = substr($date,4,2);
			$datetime->day = substr($date,6,2);
			if (($dateLen == 15) || ($dateLen == 16)) {
				$datetime->hours = substr($date,9,2);
				$datetime->minutes = substr($date,11,2);
				$datetime->seconds = substr($date,13,2);
			} else {
				$datetime->hours = 0;
				$datetime->minutes = 0;
				$datetime->seconds = 0;
			}
		}
		
		return $datetime;
	}
}