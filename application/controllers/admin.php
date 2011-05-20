<?php 
/**
 * Main Controller - for controlling all requests in the core features of the site
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Admin
 * @author		Jan Hartigan
 */
class Admin extends MY_Controller {
	
	/**
	 * Constructor...checks if user is an admin...if not, redirect to home page
	 */
	function __construct()
	{
		parent::__construct();
		
		if (!isAdmin())
			if (isAjax())
				die('Unauthorized access');
			else
				redirect(base_url());
			
		$this->selected_menu = 'admin';
		$this->load->model('articles_model');
		$this->load->model('creations_model');
		$this->load->model('portfolio_model');
	}
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->content  = $this->load->view('admin', array('admin_page'=>'admin'), true);
		$this->loadPage();
	}
	
	/**
	 * News manager
	 * 
	 * @access public
	 */
	function articles()
	{
		$articles = $this->articles_model->getItems(null, null, false);
		
		$this->content  = $this->load->view('articles/articles_manager', array('articles'=>$articles, 'admin_page'=>'articles'), true);
		$this->description = 'The articles admin page for janhartigan.com';
		$this->title = 'Admin Articles - janhartigan.com';
		
		$this->loadPage();
	}
	
	/**
	 * File manager
	 * 
	 * @access public
	 */
	function files()
	{
		$this->load->model('files_model');
		$files = $this->files_model->getFiles();
		
		$this->content  = $this->load->view('file_manager', array('files'=>$files, 'admin_page'=>'files'), true);
		$this->description = 'The files admin page for janhartigan.com';
		$this->title = 'Admin Files - janhartigan.com';
		
		$this->loadPage();
	}
	
	/**
	 * Creations manager
	 * 
	 * @access public
	 */
	function creations()
	{
		$this->load->model('creations_model');
		$creations = $this->creations_model->getCreations();
		
		$this->content  = $this->load->view('creations/creations_manager', array('creations'=>$creations, 'admin_page'=>'creations'), true);
		$this->description = 'The creations admin page for janhartigan.com';
		$this->title = 'Admin Creations - janhartigan.com';
		
		$this->loadPage();
	}
	
	/**
	 * Portfolio manager
	 * 
	 * @access public
	 */
	function portfolio()
	{
		$this->load->model('portfolio_model');
		$portfolio = $this->portfolio_model->getPortfolio(null, 0, false, 'date', false);
		
		$this->content  = $this->load->view('portfolio/portfolio_manager', array('portfolio'=>$portfolio, 'admin_page'=>'portfolio'), true);
		$this->description = 'The portfolio admin page for janhartigan.com';
		$this->title = 'Admin Portfolio - janhartigan.com';
		
		$this->loadPage();
	}
	
	
	
	////////////////////////////////////////////
	/*
	 * AJAX CALLS
	 * 
	 *//////////////////////////////////////////
	
	/**
	 * Either saves an existing news item or adds a new one...returns item data
	 */
	function saveNewsItem()
	{
		$data = $this->getAjaxData();
		
		//then attempt to save item to database
		if (isset($data['id']) && intval($data['id'])) {
			//it has an id...so it's an existing item, save it
			killScript( $this->articles_model->saveItem($data) );
		} else {
			//it's a new item, add it
			killScript( $this->articles_model->addItem($data) );
		}
	}
	
	/**
	 * Deletes a news item by its id
	 */
	function deleteNewsItem()
	{
		$data = $this->getAjaxData();
		
		if (!isset($data['id']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then attempt to delete item from database
		killScript($this->articles_model->deleteItem($data['id']));
	}
	
	/**
	 * Retrieves a news item by id
	 */
	function getNewsItem($id = null)
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get data. if it isn't there, kill script
		if (is_null($id))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		killScript($this->articles_model->getItem($id)); 
	}
	
	/**
	 * Retrieves news items
	 */
	function getNewsItems()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		killScript($this->articles_model->getItems(null, null, false)); 
	}
	
	/**
	 * Either saves an existing creation or adds a new one...returns item data
	 */
	function saveCreation()
	{
		$data = $this->getAjaxData();
		
		//then attempt to save item to database
		if (isset($data['id']) && intval($data['id']))
			//it's an existing item
			killScript($this->creations_model->saveCreation($data));
		else
			//it's a new item
			killScript($this->creations_model->addCreation($data));
	}
	
	/**
	 * Deletes a creation by its id
	 */
	function deleteCreation()
	{
		$data = $this->getAjaxData();
		
		if (!isset($data['id']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then attempt to delete item from database
		killScript($this->creations_model->deleteCreation($data['id']));
	}
	
	/**
	 * Retrieves a creation by id
	 */
	function getCreation($id = null)
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get data. if it isn't there, kill script
		if (is_null($id))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		killScript($this->creations_model->getCreation($id)); 
	}
	
	/**
	 * Retrieves creations
	 */
	function getCreations()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		killScript($this->creations_model->getCreations()); 
	}
	
	/**
	 * Retrieves files
	 */
	function getFiles()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		$this->load->model('files_model');
		killScript($this->files_model->getFiles()); 
	}
	
	/**
	 * Syncs all creations with github
	 */
	function syncCreationsWithGitHub()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		$this->load->model('creations_model');
		killScript($this->creations_model->syncWithGitHub()); 
	}

	/**
	 * Either saves an existing creation or adds a new one...returns item data
	 */
	function savePortfolioItem()
	{
		$data = $this->getAjaxData();
		
		//then attempt to save item to database
		if (isset($data['id']) && intval($data['id']))
			//it's an existing item
			killScript($this->portfolio_model->saveItem($data));
		else
			//it's a new item
			killScript($this->portfolio_model->addItem($data));
	}
	
	/**
	 * Deletes a portfolio item by its id
	 */
	function deletePortfolioItem()
	{
		$data = $this->getAjaxData();
		
		if (!isset($data['id']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then attempt to delete item from database
		killScript($this->portfolio_model->deleteItem($data['id']));
	}
	
	/**
	 * Retrieves a portfolio item by id
	 */
	function getPortfolioItem($id = null)
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get data. if it isn't there, kill script
		if (is_null($id))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		killScript($this->portfolio_model->getItem($id)); 
	}
	
	/**
	 * Retrieves the portfolio
	 */
	function getPortfolio()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		killScript($this->portfolio_model->getPortfolio()); 
	}
	
	/**
	 * Deletes files
	 */
	function deleteFiles()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get post data. if it isn't there, kill script
		$predata = $this->input->post("data");
		if (!$predata)
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then try to decode post data as json. if it doesn't work, kill script
		$data = json_decode($predata, true);
		if (!$data || !isset($data['files']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//now we pass 
		$this->load->model('files_model');
		killScript($this->files_model->deleteFiles($data['files'])); 
	}
	
	/**
	 * Makes a copy of a file
	 */
	function copyFile()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get post data. if it isn't there, kill script
		$predata = $this->input->post("data");
		if (!$predata)
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then try to decode post data as json. if it doesn't work, kill script
		$data = json_decode($predata, true);
		if (!$data || !isset($data['filename']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//now we pass 
		$this->load->model('files_model');
		killScript($this->files_model->makeFileCopy($data['filename'])); 
	}
	
	/**
	 * Resizes a file for the frontpage
	 */
	function resizeFile()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		//first get post data. if it isn't there, kill script
		$predata = $this->input->post("data");
		if (!$predata)
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//then try to decode post data as json. if it doesn't work, kill script
		$data = json_decode($predata, true);
		if (!$data || !isset($data['filename']))
			killScript(array('success'=>false, 'error'=>"No data supplied"));
		
		//now we pass filename to model and it returns data back to javascript
		$this->load->model('files_model');
		killScript($this->files_model->resizeForFrontpage($data['filename'])); 
	}
	
	/**
	 * Accepts a post array with two indexes: 'baseDirectory' and 'dir'. The baseDirectory is the base folder relative to the wwwroot 
	 * that the dir will be relative to. The dir is the addition to the baseDirectory. For example, a baseDirectory of '/images' and 
	 * a dir of '/creations' will look for the contents of the folder '/images/creations'.
	 * 
	 * @param string	baseDirectory
	 * @param string	dir
	 * 
	 * @return json		{'success':bool ? 'directoryContents': {
	 * 															'type':'directory',
	 * 															'path':'/creations/creation1'
	 * 															}
	 * 									: 'error': string}
	 */
	function getDirectory()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		$baseDirectory = $this->input->post('baseDirectory');
		$dir = $this->input->post('dir');
		
		$this->load->model('files_model');
		killScript($this->files_model->getDirectoryContents($baseDirectory.$dir));
	}
}
?>