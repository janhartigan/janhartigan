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
		//$this->content  = $this->load->view('portfolio/portfolio_home', $data, true);
		$this->content  = '';
		$this->loadPage();
	}
}
?>