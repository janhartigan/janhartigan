<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Takes an array, usually with a 'success' and 'result' index (more if necessary), encodes it to json, and die()s the script
 * 
 * @param	array
 */
if ( ! function_exists('killScript')) {
	function killScript($returnObj) {
		$returnStr = json_encode($returnObj);
		die($returnStr);
	}
}
?>