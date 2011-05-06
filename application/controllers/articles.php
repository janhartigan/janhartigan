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
	 * @param int		page
	 * 
	 * @access public
	 */
	function index($page = 1)
	{
		$data = array(
					'articles'=>$this->articles_model->getItems(10, $page)
				);
		
		$this->content = $this->load->view('articles/articles', $data, true);
		$this->loadPage();
	}
	
	/**
	 * This displays individual articles
	 * 
	 * @param string	title_url
	 */
	function article($title_url='')
	{
		//ghetto ass solution to the routing problem...works well for now
		if (is_numeric($title_url))
		{
			$this->index($title_url);
			return;
		}
		
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