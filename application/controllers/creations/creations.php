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
	 * Constructor function
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('creations_model');
	} 
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index($page=1, $sort='date')
	{
		$data = array(	 
						'creations'=>$this->creations_model->getCreations(10, $page, true, $sort) 
					);
		
		$this->description = 'The creations and experiments of Jan Hartigan';
		$this->title = 'Creations - janhartigan.com';
		$this->image = '/images/creations_icon.png';
		$this->selected_menu = 'creations';
		$this->content  = $this->load->view('creations_home', $data, true);
		$this->loadPage();
	}
}
?>