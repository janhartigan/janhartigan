<?php 
/**
 * Articles Controller - for controlling all articles requests
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Articles
 * @author		Jan Hartigan
 */
class Articles extends MY_Controller {
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->selected_menu = 'articles';
		$this->load->model('articles_model');
	}
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->content = $this->load->view('articles/articles', '', true);
		$this->selected_menu = 'articles';
		$this->loadPage();
	}
	
	/**
	 * This displays individual articles
	 * 
	 * @param string	title_url
	 */
	function article($title_url='')
	{
		$item = $this->articles_model->getItemByTitleUrl($title_url);
		
		if (empty($title_url) || !$item['success'])
			redirect('articles');
		
		$this->content = $this->load->view('articles/article', array('item'=>$item['item']), true);
		$this->description = $item['item']['description'];
		$this->title = $item['item']['title'].' - janhartigan.com';
		$this->disqus = 'article-'.$title_url;
		$this->loadPage();
	}
}
?>