<?php 
/**
 * Main Controller - for controlling all requests in the core features of the site
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Main
 * @author		Jan Hartigan
 */
class Main extends MY_Controller {
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->load->model('articles_model');
		$this->load->model('creations_model');
		$this->load->model('portfolio_model');
		
		$data = array(	'articles'=>$this->articles_model->getItems(5), 
						'creations'=>$this->creations_model->getCreations(3, 1, true),
						'portfolio'=>$this->portfolio_model->getPortfolio(3, 1, true)
					);
		
		$this->content  = $this->load->view('homepage', $data, true);
		$this->loadPage();
	}
	
	/**
	 * 404 error page
	 */
	function error404()
	{
		$this->title = "Y u no exist, page?? - janhartigan.com";
		$this->content  = $this->load->view('page_404', false, true);
		$this->loadPage();
	}
}
?>