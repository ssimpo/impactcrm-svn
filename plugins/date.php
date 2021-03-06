<?php
defined('DIRECT_ACCESS_CHECK') or die;

class Plugin_Date implements Impact_Plugin {

	public function run($attributes) {
		
		$HTML = '';
		$date = date_create();
		$format = 'jS M Y';
		
		if (array_key_exists('modify',$attributes)) {
			if (!isEqual($attributes['modify'],"")) {
				$date->modify($attributes['modify']);
			}
		}
		if (array_key_exists('format',$attributes)) {
			if (!isEqual($attributes['format'],"")) {
				$format = $attributes['format'];
			}
		}
		
		return date_format($date,$format);
	}
	
}
?>