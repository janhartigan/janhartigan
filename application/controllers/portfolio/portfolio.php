<?php 
/**
 * Creations Controller - for controlling all requests on the creations page
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Portfolio
 * @author		Jan Hartigan
 */
class Portfolio extends MY_Controller {
	
	/**
	 * Constructor function
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('portfolio_model');
	} 
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index($page=1, $sort='date')
	{
		$data = array(	 
						'portfolio'=>$this->portfolio_model->getPortfolio(10, $page, true, $sort) 
					);
		
		$this->description = 'The portfolio of Jan Hartigan';
		$this->title = 'Portfolio - janhartigan.com';
		$this->image = '/images/portfolio_icon.png';
		$this->selected_menu = 'portfolio';
		$this->content  = $this->load->view('portfolio/portfolio_home', $data, true);
		$this->loadPage();
	}
	
	/**
	 * portfolio item function
	 * 
	 * @access public
	 */
	function item($uri='')
	{
		$item = $this->portfolio_model->getItemByUri($uri);
		
		if (empty($uri) || !$item['success'])
			redirect('portfolio');
		
		$data = array(
					'item'=>$item['item'] 
				);
		
		
		
		$this->description = $data['item']['short_description'];
		$this->title = $data['item']['name'].' - Portfolio - janhartigan.com';
		$this->image = $data['item']['image_small'];
		$this->selected_menu = 'portfolio';
		$this->disqus = 'portfolio-'.$data['item']['uri'];
		$this->content  = $this->load->view('portfolio/portfolio_item', $data, true);
		$this->loadPage();
	}
}
?>