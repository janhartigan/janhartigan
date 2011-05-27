<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* isAdmin call that pings the user model to determine if this user is an admin
*
* @access    public
* @param    void
* @return    boolean
*/
if ( ! function_exists('isAdmin')) {
    function isAdmin() {
		$CI =& get_instance();
		
		return $CI->user_model->isAdmin();
	}
}
?>