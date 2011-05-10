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
			'articles' => $this->articles_model->getItems(5, $page),
			'page' => $page,
			'list_style' => 'blurb'
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
		
		if (empty($title_url))
			redirect('articles');
		else if (!$item['success']) {
			if (method_exists($this, $title_url)) {
				$this->$title_url();
				return;
			} else
				redirect('articles');
		}
		
		//temporary workaround for the about page
		if ($item['id'] == 26)
			$this->selected_menu = 'about';
		
		$this->content = $this->load->view('articles/article', array('item'=>$item['item']), true);
		$this->description = $item['item']['description'];
		$this->title = $item['item']['title'].' - janhartigan.com';
		$this->disqus = 'article-'.$title_url;
		$this->loadPage();
	}
	
	/**
	 * AJAX handler for getting the article list given a bunch of different parameters
	 */
	function getArticles()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		$data = $this->getAjaxData();
		
		//sterilize the data a bit before sending it off to the model. this is also for later use in the articles view
		$data = array(
			//ensuring that the value is a number and that it's above 0
			'page' => ( intval($data['page']) > 0 ? intval($data['page']) : 1 ),
			
			//the list_style to be used
			'list_style' => strval($data['list_style']) == 'headlines' ? 'headlines' : 'blurb'
		);
		
		//if it's the 'blurb' style, do 5 items. if it's the 'headlines', do 20
		$num_rows = $data['list_style'] == 'blurb' ? 5 : 20;
		
		//then get info with supplied params, send them to the articles view, and kill the return back to the browser
		$data['articles'] = $this->articles_model->getItems($num_rows, $data['page']);
		
		killScript( array(
			'success' => true, 
			'content' => $this->load->view('articles/article_list', $data, true) 
		) );
	}
}
?>