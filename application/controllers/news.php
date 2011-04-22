<?php 
/**
 * News Controller - for controlling all news requests
 *
 * @package		hlcc
 * @subpackage	Controllers
 * @category	News
 * @author		Jan Hartigan
 */
class News extends MY_Controller {
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('news_model');
	}
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->content = $this->load->view('news', '', true);
		$this->loadPage();
	}
	
	/**
	 * This displays individual articles
	 * 
	 * @param string	title_url
	 */
	function article($title_url='')
	{
		$item = $this->news_model->getItemByTitleUrl($title_url);
		
		if (empty($title_url) || !$item['success'])
			redirect('news');
		
		$this->content = $this->load->view('news_item', array('item'=>$item['item']), true);
		$this->description = $item['item']['description'];
		$this->title = $item['item']['title'].' - janhartigan.com';
		$this->disqus = 'news-'.$title_url;
		$this->loadPage();
	}
}
?>