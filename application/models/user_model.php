<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model {
	
	/**
	 * Creates a new user given username and password, when registering without open id
	 *
	 * @param string 	username
	 * @param string 	password - unencrypted
	 * @param string 	email
	 * 
	 * @return array('success'=>bool, 'result'=>username/errormessage)
	 */
	function createUserByUsername($username, $password, $email = null) {
		$result = $this->getUserByUsername($username);
		
		if ($result['success']) {
			return array('success'=>false, 'error'=>'This username is already in use.');
		} else {
			$salt = rand(0, 1000000);
			$passhash = hash("sha256", $password.$salt);

			$query = "INSERT INTO users (username, password, salt, email) VALUES (?, ?, ?, ?)";
			$result = $this->db->query($query, array($username, $passhash, $salt, $email));
			
			if (!$result)
				return array('success'=>false, 'error'=>"Whoops...there was an error creating this user. Please try it again.");
			
			$user_id = $this->db->insert_id();
			
			$this->setSessionCookie($user_id, $username);
			$this->logUser();
			
			$this->load->library('email');
			$this->email->from('info@aptgif.com', 'aptgif');
			$this->email->to($email);
			
			$this->email->subject('Welcome to aptgif');
			$this->email->message("Hi ".$username.",<br/><br/>Thanks for registering with <a href='".base_url()."'>aptgif</a>. Now that you're a member, you 
				have access to user features that you can read about <a href='".base_url()."whyuser/'>here</a>. Otherwise, you're all set up to go. If you 
				have any questions, just reply to this email and we'll get back to you as soon as possible.<br/><br/>If you did not sign up for aptgif.com, or if 
				you think that this email was sent to you by mistake, please send us a quick message and we'll remove every last trace of your 
				email address from our records.<br/><br/>Thanks,<br/>aptgif.com<br/>");
			
			$this->email->send();
			return array('success'=>true, 'user_id'=>$user_id);
		}
	}
	
	/**
	 * Attempts to log a user in given username and password
	 *
	 * @param string username
	 * @param string password - unencrypted  
	 * 
	 * @return array('success'=>bool, 'error'=>errormessage, 'username'=>username)
	 */
	function checkUsernamePassword($username, $password) {
		$getUser = $this->getUserByUsername($username);
		
		if (!$getUser['success']) {
			return array('success'=>false, 'error'=>"This username doesn't exist");
		}
		
		$user = $getUser['result'];
		$salt = $user['salt'];
		$passhash = hash("sha256", $password.$salt);
		
		if ($passhash == $user['password']) {
			$this->setSessionCookie($user['id'], $username);
			$this->logUser();
				
			return array('success'=>true);
		} else {
			return array('success'=>false, 'error'=>"This password is incorrect");
		}
	}
	
	/**
	 * Sends a password reset request email given a user_id
	 *
	 * @param int	user_id
	 * 
	 * @return bool
	 */
	function resetPassword($user_id) 
	{
		$user = $this->getUser($user_id);
		
		if ($user) 
		{
			$salt = $user['salt'];
			$resethex = dechex(mt_rand(100000, 10000000000));
			$resetkey = md5($resethex.$salt);
			
			$query = "UPDATE users 
						SET reset_key = ? 
					  WHERE id = ?";
			
			$this->db->query($query, array($resetkey, $user_id));
			
			$this->load->library('email');
			$this->email->from('passwordreset@aptgif.com', 'AptGif.com');
			$this->email->to($user['email']);
			
			$usermessage = $user['username'] ? "(".$user['username'].") " : "";
			
			$this->email->subject('Password reset');
			$this->email->message("Someone has submitted a password reset request for the user associated with this email address ".$usermessage."at AptGif.com. 
									If this was you, please click the following link where you will be able to obtain a new password:
									\n\n"
									.$base_url."user/resetconfirmed/".$resetkey."
									\n\n
									If you did not submit this request, please ignore this and we'll try to make sure that the person who did submit it is tracked 
									down and executed without due process.");
			
			return $this->email->send();
		} else {
			return false;
		}
	}
	
	/**
	 * Gets user information or returns false if user_id doesn't exist
	 * 
	 * @param int	$user_id
	 * 
	 * @return mixed	bool / user information in associative array
	 */
	function getUser($user_id = null) 
	{
		if (is_null($user_id))
		{
			$user_id = $this->session->userdata('user_id');
			
			if (!$user_id)
				return false;
		}
		
		$qStr = "SELECT * FROM users WHERE id=?";
		$q = $this->db->query($qStr, array($user_id));
		
		if ($q->num_rows() > 0) 
			return $q->row_array();
		else 
			return false;
	}
	
	/**
	 * Returns a user array for the currently-signed-in user
	 * 
	 * @return array	if no user found, just returns array('logged_in'=>false)
	 */
	function getCurrentUser()
	{
		$user_id = $this->session->userdata('user_id');
		
		if (!$user_id)
			return array('logged_in'=>false);
		
		$user = $this->getUser($user_id);
		
		if ($user) {
			$user['logged_in'] = true;
			return $user;
		} else
			return array('logged_in'=>false);
	}
	
	/**
	 * Gets user information or returns false if username doesn't exist
	 * 
	 * @param string	$username
	 * 
	 * @return mixed	bool / user information in associative array
	 */
	function getUserByUsername($username = null) 
	{
		$qStr = "SELECT * FROM users WHERE username=?";
		$q = $this->db->query($qStr, array($username));
		
		if ($q->num_rows() > 0) 
			return array('success'=>true, 'result'=>$q->row_array());
		else 
			return array('success'=>false, 'error'=>"This username does not exist");
	}
	
	/**
	 * Sets session cookie data
	 * 
	 * @param int 		$user_id
	 * @param string	$username
	 * 
	 * @return bool
	 */
	private function setSessionCookie($user_id, $username) 
	{
		$this->session->set_userdata(array('user_id'=>$user_id, 'username'=>$username, 'logged_in'=>TRUE));
		return true;
	}
	
	/**
	 * Logs a user's login into the user_login table
	 * 
	 * @return bool
	 */
	private function logUser() 
	{
		$query = "INSERT INTO user_login (user_id, ip_address, agent, platform, agent_string) VALUES(?, ?, ?, ?, ?)";
		$q = $this->db->query($query, array($this->session->userdata('user_id'), $this->session->userdata('ip_address'), $this->getUserAgent(), 
											$this->agent->platform(), $this->agent->agent_string()));
		
		return $q;
	}
	
	/**
	 * returns the user's user_agent
	 * 
	 * @return string	user_agent
	 */
	private function getUserAgent() 
	{
		$this->load->library('user_agent');

		if ($this->agent->is_browser())	{
		    $agent = $this->agent->browser().' '.$this->agent->version();
		} elseif ($this->agent->is_robot()) {
		    $agent = $this->agent->robot();
		} elseif ($this->agent->is_mobile()) {
		    $agent = $this->agent->mobile();
		} else {
		    $agent = 'Unidentified User Agent';
		}
		
		return $agent;
	}
	
	/**
	 * Checks if a user is an admin
	 * 
	 * @param int	user_id
	 * 
	 * @return bool 	true if admin, false otherwise
	 */
	function isAdmin($user_id=null) 
	{
		if (is_null($user_id)) {
			if (!$this->session->userdata('logged_in')) 
			{
				return false;
			} else 
			{
				$user_id = $this->session->userdata('user_id');
			}
		}
		
		$qStr = "SELECT id, admin FROM users WHERE id=?";
		$q = $this->db->query($qStr, array($user_id));
		
		if ($q->num_rows() > 0) {
			$row = $q->row_array();
			
			return $row['admin'];
		} else {
			return false;
		}
	}

	/**
	 * Sets a new password for a user
	 * 
	 * @param int	 	user_id
	 * @param string	password
	 * 
	 * @return bool
	 */
	function setPassword($user_id, $password) 
	{
		$salt = rand(0, 1000000);
		$passhash = hash("sha256", $password.$salt);
		
		$qStr = "UPDATE users SET password=?, salt=? WHERE id=?";
		$q = $this->db->query($qStr, array($passhash, $salt, $user_id));
		
		return $q && $this->db->affected_rows();
	}

	/**
	 * Sets a new email for a user
	 *
	 * @param int	 	user_id
	 * @param string 	email
	 * 
	 * @return bool
	 */
	function setEmail($user_id, $email) 
	{
		$qStr = "UPDATE users SET email=? WHERE id=?";
		$q = $this->db->query($qStr, array($email, $user_id));
		
		return $q && $this->db->affected_rows();
	}

	/**
	 * Retrieve user's email
	 * 
	 * @param int	user_id
	 * 
	 * @return mixed	false / email string
	*/
	function getEmail($user_id = null)
	{
		if (is_null($user_id))
		{
			$user_id = $this->session->userdata('user_id');
		}
		
		$qStr = "SELECT email FROM users WHERE id=?";
		$q = $this->db->query($qStr , array($user_id));
		
		if ($q && $q->num_rows() > 0)
		{
			$r = $q->row();
			return $r->email;
		} else return false;
	}
	
	/**
	 * Adds contact information information to the database and sends emails about it
	 * 
	 * @param ???
	 */
	
	function submitContact($info)
	{
		$q="INSERT INTO user_queries (name,email,subject,comment) VALUES (?,?,?,?)";
		$r=$this->db->query($q,array($info['name'],$info['email'],$info['subject'],$info['comment']));
		if($r){
			$this->load->library('email');
			$this->email->set_newline("\r\n");
			$this->email->from('info@plurty.com', 'Customer Contact');
			$this->email->to('info@plurty.com');
			$this->email->cc('nixkelly@gmail.com');
		
			$this->email->subject('Plurty Contact: '.$info['subject']);
			$this->email->message('USER:'.$info['name'].'<br/>'.$info['comment']);
		
			$this->email->send();
			return true;
		}
		else {
			return false;
		}
	}
}
?>