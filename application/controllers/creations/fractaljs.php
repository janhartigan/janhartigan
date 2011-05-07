<?php 
/**
 * Fractal Controller for the FractalJS creation
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Fractal
 * @author		Jan Hartigan
 */
class FractalJS extends MY_Controller 
{
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->load->model('creations_model');
		
		$creation = $this->creations_model->getCreationByUri('fractaljs');
		
		$data = array(
			'creation_view' => 'creations/fractaljs/',
			'creation' => $creation,
			'creation_tools' => $this->creations_model->getCreationTools($creation['creation']['id'])
		);
		
		$this->description = $creation['creation']['short_description'];
		$this->title = $creation['creation']['name'].' - janhartigan.com';
		$this->selected_menu = 'creations';
		$this->image = substr($creation['creation']['image'], 1);
		$this->disqus = 'creations-fractaljs';
		$this->content  = $this->load->view('creations/creation', $data, true);
		$this->js_files[] = 'creations/fractaljs/jquery.fractaljs.min.js';
		$this->js_files[] = 'creations/fractaljs/example.js';
		$this->css_files[] = 'creations/fractaljs/example.css';
		
		$this->loadPage();
	}
}