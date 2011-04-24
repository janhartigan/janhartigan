<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class MY_Controller extends CI_Controller {
	
	/** 
	 * html content of the page
	 * @type string
	 */
	var $content = "";
	
	/** 
	 * title of the current page
	 * @type string
	 */
	var $title = "janhartigan.com - where your dreams probably won't come true";
	
	/** 
	 * The selected menu item
	 * @type string
	 */
	var $selected_menu = "home";
	
	/** 
	 * description of the current page
	 * @type string
	 */
	var $description = "The personal website of Jan Hartigan in which many fancy web tricks and interesting subjects are explored.";
	
	/** 
	 * image of the current page
	 * @type string
	 */
	var $image = "";
	
	/** 
	 * array of js files to insert onto the page
	 * @type array
	 */
	var $js_files = array();
	
	/** 
	 * array of css files to insert onto the page
	 * @type array
	 */
	var $css_files = array();
	
	/**
	 * disqus unique identifier (also the uri)
	 * @type string
	 */
	var $disqus = '';
	
	function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * handles get requests sort of like the input->post (this should really extend the input library...)
	 * 
	 * @param string	param
	 * 
	 * @return mixed	(false if param doesn't exist / value of thing if it exists)
	 */
	function get($param)
	{
		//check if param is set
		if (!isset($param) || empty($param))
		{
			return false;
		}
		
		//set get array if not already set
		if (!isset($_GET) || empty($_GET))
		{
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}

		//if the index isn't there in the get array, return false, otherwise return valeu
		if (isset($_GET[$param]))
		{
			$this->load->library('security');
			return $this->security->xss_clean($_GET[$param]);
		} else
		{
			return false;
		}
	}
	
	/**
	 * Loads a page given an array with a 'content' key holding the html for the inner box
	 */
	public function loadPage() {
		$data = array(
			'isadmin' => $this->user_model->isAdmin(),
			'window_title' => $this->title,
			'window_image' => $this->image,
			'window_description' => $this->description,
			'selected_menu' => $this->selected_menu,
			'user' => $this->user_model->getCurrentUser(),
			'content' => $this->content,
			'js_files' => $this->js_files,
			'css_files' => $this->css_files,
			'disqus' => $this->disqus
		);
		
		if(isset($data['cache'])){
			$this->output->cache($data['cache']);
		}
		
		$this->load->view('header.php', $data);
		$this->load->view('scaffold.php', $data);
		$this->load->view('footer.php', $data);
	}
	
	/**
	 * Gets AJAX data...includes an isAjax() call with a redirect if it isn't AJAX. Looks for a value called "data"
	 * 
	 * @param string	var
	 * 
	 * @return mixed	json-supplied data
	 */
	function getAjaxData($name='data')
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get post data. if it isn't there, kill script
		$predata = $this->input->post($name);
		if (!$predata)
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then try to decode post data as json. if it doesn't work, kill script
		$data = json_decode($predata, true);
		
		if (is_null($data))
			killScript(array('success'=>false, 'error'=>"Improper data supplied"));
		
		return $data;
	}
}
?>