<?php

class Upload extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}
	
	function index()
	{	
		redirect('');
	}

	function file()
	{
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp';
		
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload('file'))
			die(
				'<textarea>'.
				json_encode(array('success'=>false, 'error'=>$this->upload->display_errors())).
				'</textarea>'
			);
		else
			//wrap in textarea to get around iframe json bug
			die(
				'<textarea>'.
				json_encode(array('success'=>true, 'image'=>$this->upload->data())).
				'</textarea>'
			);
	}
}
?>