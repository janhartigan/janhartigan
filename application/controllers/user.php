<?php 
/**
 * User Controller - for controlling all user requests
 *
 * @package		aptgif
 * @subpackage	Controllers
 * @category	User
 * @author		Jan Hartigan
 */
class User extends MY_Controller {
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		redirect('');
	}
	
	/**
	 * register/login page
	 */
	function login()
	{
		$this->title = "Login - janhartigan.com";
		$this->description = "Login page for janhartigan.com";
		$this->content = $this->load->view('login', '', true);
		$this->loadPage();
	}
	
	/**
	 * Logout function
	 */
	public function logout() {
		$this->session->sess_destroy();
		redirect($this->agent->referrer(), 'location');
	}
	
	/**
	 * Function run when registration form is submitted
	 * @url /user/registersubmit
	 * 
	 * @param string	username
	 * @param string	password
	 * @param string	verify_password
	 * @param string	email
	 * @param string	return_url
	 * 
	 * @return either a json object with success/error slots and data or redirects to redirect page
	 */
	public function registerSubmit() 
	{
		$this->title = "login / register";
		redirect(''); die();
		
		if (isAjax()) {
			//take ajax-submitted user data and check against db
			$data = json_decode($this->input->post('data'));
			
			$username = $data->username;
			$password = $data->password;
			$verify_password = $data->verify_password;
			$email = $data->email;
			
			if ($this->session->userdata('logged_in'))
				killScript(array('success'=>true));
			
			killScript($this->user_model->createUserByUsername($username, $password, $email));
		} else {
			//take form-submitted data 
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$verify_password = $this->input->post('verify_password');
			$email = $this->input->post('email');
			$return_url = $this->input->post('return_url');
			
			//if user is already logged in, redirect to return_url
			if ($this->session->userdata('logged_in'))
				redirect($return_url, 'location');
			
			//Check if passwords match
			if (trim($password) !== trim($password))
			{
				$data = array('register'=>true, 'error'=>"Passwords do not match");
				$this->content = $this->load->view('login', $data, true);
				$this->loadPage(); return false;
			}
			
			//create user
			$result = $this->user_model->createUserByUsername($username, $password, $email);
			
			if ($result['success']) {
				redirect($return_url, 'location');
			} else {
				$data = array('register'=>true, 'error'=>$result['error']);
				$this->content = $this->load->view('register', $data, true);
				$this->loadPage(); return false;
			}
		}
	}
	
	/**
	 * Function run upon submission of login form
	 * @url /user/registersubmit
	 * 
	 * @param string	username
	 * @param string	password
	 * 
	 * @return json-encoded array('success'=>bool, 'result'=>username, 'error'=>errormessage) or redirect to redirect url
	 * @url /user/loginsubmit
	 */
	public function loginSubmit() 
	{
		if (isAjax()) {
			//take ajax-submitted user data and check against db
			$data = json_decode($this->input->post('data'));
			
			$username = $data->username;
			$password = $data->password;
			$return_url = $data->return_url;
			
			if ($this->session->userdata('logged_in'))
				killScript(array('success'=>true));
			
			killScript($this->user_model->checkUsernamePassword($username, $password));
		} else {
			//take form-submitted data 
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$return_url = $this->input->post('return_url');
			
			if ($this->session->userdata('logged_in'))
				redirect($return_url, 'location');
			
			$result = $this->user_model->checkUsernamePassword($username, $password);
			
			if ($result['success']) {
				redirect('', 'location');
				die;
			} else {
				$data = array();
				$data['login'] = true;
				$data['error'] = $result['result'];
				$this->content = $this->load->view('login', $data, true);
				
				$this->loadPage();
			}
		}
	}
}

?>