<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class Userlib {
	var $CI;
	var $admin;
	
	function Userlib() {
		$this->CI =& get_instance();
	}
	
	/**
	 * checks if a user is valid
	 * 
	 * @return bool
	 */
	public function checkUser($username=null) 
	{
		if (is_null($username)) {
			if (!$this->CI->session->userdata('logged_in')) {
				return false;
			}
			
			$username = $this->CI->session->userdata('user_id');
		}
		
		$userExists = $this->CI->user_model->userExists($username);
		
		if ($userExists['success']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Checks if a user is an admin
	 */
	function isAdmin()
	{
		if (!isset($this->admin))
		{
			$this->admin = $this->CI->user_model->isAdmin();
		}
		
		return $this->admin;
	}
}
?>