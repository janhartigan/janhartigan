<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* is_valid_url
*
* Determines if a string is a valid url and returns only the valid part if it finds a match
*
* @param string		string
* 
* @return bool
*/
if ( ! function_exists('is_valid_url')) {
	function is_valid_url($string) {
		$reg = '@\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))@';
		return preg_match($reg, $string);
	}
}
?>