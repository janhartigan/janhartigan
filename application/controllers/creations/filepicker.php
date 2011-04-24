<?php 
/**
 * filepicker Controller for the filepicker creation
 *
 * @package		janhartigan
 * @subpackage	Controllers
 * @category	Creation
 * @author		Jan Hartigan
 */
class filepicker extends MY_Controller 
{
	
	/**
	 * index function
	 * 
	 * @access public
	 */
	function index()
	{
		$this->load->model('creations_model');
		
		$creation = $this->creations_model->getCreationByUri('filepicker');
		
		$data = array(
			'creation_view' => 'creations/filepicker/',
			'creation' => $creation,
			'creation_tools' => $this->creations_model->getCreationTools($creation['creation']['id'])
		);
		
		$this->description = $creation['creation']['short_description'];
		$this->title = $creation['creation']['name'].' - janhartigan.com';
		$this->image = substr($creation['creation']['image'], 1);
		$this->selected_menu = 'creations';
		$this->disqus = 'creations-filepicker';
		$this->content  = $this->load->view('creations/creation_home', $data, true);
		$this->js_files[] = 'creations/filepicker/jquery.filepicker.js';
		$this->js_files[] = 'creations/filepicker/setup.js';
		$this->css_files[] = 'creations/filepicker/jquery.filepicker.css';
		$this->css_files[] = 'creations/filepicker/example.css';
		
		$this->loadPage();
	}
	
	/**
	 * Accepts a post array with two indexes: 'baseDirectory' and 'dir'. The baseDirectory is the base folder relative to the wwwroot 
	 * that the dir will be relative to. The dir is the addition to the baseDirectory. For example, a baseDirectory of '/images' and 
	 * a dir of '/creations' will look for the contents of the folder '/images/creations'. However, since this is just an example, we always
	 * default to /images/creations
	 * 
	 * @param string	baseDirectory
	 * @param string	dir
	 * 
	 * @return json		{'success':bool ? 'directoryContents': {
	 * 															'type':'directory',
	 * 															'path':'/creations/creation1'
	 * 															}
	 * 									: 'error': string}
	 */
	function getDirectory()
	{
		//if it isn't ajax, redirect to home page
		if (!isAjax())
			redirect('');
		
		$baseDirectory = '/images/creations';
		$dir = $this->input->post('dir');
		
		$this->load->model('files_model');
		killScript($this->files_model->getDirectoryContents($baseDirectory.$dir));
	}
}