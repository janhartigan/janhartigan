<?php 
/**
 * Creations Controller - for controlling all requests on the creations page
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Creations
 * @author		Jan Hartigan
 */
class Creations extends MY_Controller {
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->load->model('news_model');
		$this->load->model('creations_model');
		
		$data = array(	'news_items'=>$this->news_model->getItems(5), 
						'creations'=>$this->creations_model->getCreations(10, 1, true) 
					);
		
		$this->content  = $this->load->view('homepage', $data, true);
		$this->loadPage();
	}
}
?>