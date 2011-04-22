<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Takes an array, usually with a 'success' and 'result' index (more if necessary), encodes it to json, and die()s the script
 * 
 * @param	array
 */
if ( ! function_exists('curr_url')) {
	function curr_url() {
		$pageURL = 'http';
		
		//if ($_SERVER["HTTPS"] == "on")
		//	$pageURL .= "s";
		
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else 
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		return $pageURL;
	}
}
?>