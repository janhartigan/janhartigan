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
		$data['isadmin'] = $this->user_model->isAdmin();
		
		if (!$data['isadmin']) {
			//$page_title = "Login/Register";
			//$data['content'] = $this->load->view('login.php', $data, true);
		} else {
			//$data['inventory_menu'] = $this->load->view('inventorymenu.php', '', true);
		}
		
		$data['window_title'] = $this->title;
		$data['window_image'] = $this->image;
		$data['window_description'] = $this->description;
		$data['user'] = $this->user_model->getCurrentUser();
		$data['content'] = $this->content;
		$data['js_files'] = $this->js_files;
		$data['css_files'] = $this->css_files;
		$data['disqus'] = $this->disqus;
		//$data['register_window'] = $this->load->view('login.php', '', true);
		if(isset($data['cache'])){
			$this->output->cache($data['cache']);
		}
		$this->load->view('header.php', $data);
		$this->load->view('scaffold.php', $data);
		$this->load->view('footer.php', $data);
	}
	
	/**
	 * Loads the 404 page
	 */
	public function load_404() {
		$data['isadmin'] = $this->UserM->isAdmin();
		
		if ($data['isadmin']) {
			$data['inventory_menu'] = $this->load->view('inventorymenu.php', '', true);
		}
		
		$uri_string = uri_string();
		
		$data['window_title'] = $this->title;
		$data['window_image'] = $this->image;
		$data['window_description'] = $this->description;
		$data['agent'] = $this->agent->browser();
		$data['logged_in'] = $this->session->userdata('logged_in');
		$data['user_id'] = $this->session->userdata('user_id');
		$data['categories'] = $this->categories->getCategories();
		$data['cart'] = $this->cartm->getCart();
		$data['side_cart'] = $this->load->view('sidecart.php', $data, true);
		$data['breadcrumbs'] = $this->breadcrumbs->getCrumbs();
		$data['register_window'] = $this->load->view('login.php', '', true);
			$info['tags']=$this->categories->getTags();
			$info['maxTags']=100;
		$data['menu']=$this->load->view('tagMenu.php',$info,true);
		$this->load->library('text');
		$data['error'] = $this->text->convertToHtml("Dearest internet denizen,
		
		The page you have requested (_*".current_url()."*_) doesn't exist. To navigate the site, just use the the pretty buttons and stuff.
		
		Love,
		
		Jan");
		
		$data['content'] = $this->load->view('404.php', $data, true);
		
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