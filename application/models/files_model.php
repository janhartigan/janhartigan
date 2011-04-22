<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files_Model extends CI_Model {
	//absolute path prefix
	var $prefix = '';
	
	function __construct()
	{
		 $this->prefix = FCPATH.'uploads'.DIRECTORY_SEPARATOR;
		 parent::__construct();
	}
	
	/**
	 * Gets all files by directory
	 * 
	 * @param string	directory (at base)
	 * 
	 * @return array('success' ? 'files' => array of file info : 'error')
	 */
	function getFiles($folder = 'uploads')
	{
		$directory = FCPATH.$folder;
		$scan = scandir($directory);
		$file_array = array();
		
		if ($scan)
		{
			$i = 1;
			
			foreach ($scan as $file)
			{
				if ($file == '.' || $file == '..' || strpos($file, '.') === false)
					continue;
				
				$filepath = $directory.DIRECTORY_SEPARATOR.$file;
				$url = base_url().$folder.'/'.$file;
				$type = $this->getFileType($filepath);
				
				if (!$type)
					continue;
				
				$file_array[] = array(	'rowId'=>$i,
										'name'=>$file, 
										'url'=>$url, 
										'size'=>ceil(filesize($filepath)/1000), 
										'type'=>$type,
										'date'=>date('Y-m-d', filemtime($filepath)));
				$i++;
			}
			
			return array('success'=>true, 'files'=>$file_array);
		}
		else
		{
			return array('success'=>false, 'error'=>"This directory does not exist");
		}
	}
	
	/**
	 * Gets all contents of a directory
	 * 
	 * @param string	directory (at base)
	 * 
	 * @return array('success' ? 'files' => array of file info : 'error')
	 */
	function getDirectoryContents($folder = '')
	{
		//strip the leading '/' if it exists
		$folder = (strpos($folder, '/') === 0 ? substr($folder, 1) : $folder);
		
		$directory = FCPATH.$folder;
		$scan = scandir($directory);
		$contents_array = array();
		
		if ($scan)
		{
			foreach ($scan as $file)
			{
				if ($file == '.' || $file == '..')
					continue;
				
				$filepath = $directory.DIRECTORY_SEPARATOR.$file;
				$path = DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$file;
				$type = @filetype($filepath);
				
				if (!$type)
					continue;
					
				$contents_array[] = array(
										'name'=>$file,
										'path'=>$path, 
										'size'=>ceil(filesize($filepath)/1000), 
										'type'=>$type,
										'fileType'=>$this->getFileType($filepath),
										'date'=>date('Y-m-d H:i:s', filemtime($filepath)));
			}
			
			return array('success'=>true, 'contents'=>$contents_array);
		}
		else
			return array('success'=>false, 'error'=>"This directory does not exist");
	}
	
	/**
	 * Attempts to get a file's type returning "file" if it can't be recognized
	 * 
	 * @param string	filepath
	 * @param bool		exclude_folders
	 * 
	 * @return mixed	file_type (string) or false
	 */
	private function getFileType($filepath)
	{
		if (!is_file($filepath) || filetype($filepath) != 'file')
			return false;
		
		$exif = exif_imagetype($filepath);
		$type = '';
		
		switch($exif) {
			case IMAGETYPE_GIF:
			case IMAGETYPE_JPEG:
			case IMAGETYPE_PNG:
			case IMAGETYPE_BMP:
			case IMAGETYPE_WBMP: $type = 'image'; break;
			case IMAGETYPE_SWF: $type = 'flash'; break;
			case IMAGETYPE_PSD: $type = 'photoshop'; break;
			default: $type = 'file'; break;
		}
		
		return $type;
	}
	
	/**
	 * Deletes a list of files
	 * 
	 * @param array		files
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function deleteFiles($files, $folder = 'uploads')
	{
		if (empty($files))
			return array('success'=>false, 'error'=>"Please select a file to be deleted");
		
		$directory = FCPATH.$folder;
		
		foreach($files as $file)
		{
			$filepath = $directory.DIRECTORY_SEPARATOR.$file;
			
			if (is_file($filepath))
			{
				@unlink($filepath);
			}
		}
		
		return array('success'=>true);
	}
	
	/**
	 * Creates a copy of a file
	 * 
	 * @param string	filename
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function makeFileCopy($filename)
	{
		$this->load->library('upload');
		
		$prefix = FCPATH.'uploads'.DIRECTORY_SEPARATOR;
		$new_filename = '';
		$ext = $this->upload->get_extension($filename);
		$filename_first = str_replace($ext, '', $filename);
		
		//check if old file exists
		if (!is_file($prefix.$filename))
			return array('success'=>false, 'error'=>"This file does not exist");
		
		//create new filename
		$good_location = false; $i = 1;
		while (!$good_location)
		{
			$new_filename = $filename_first.$i.$ext;
			
			if (!is_file($prefix.$new_filename))
				$good_location = true;
			
			$i++;
		}
		
		if ( copy($prefix.$filename, $prefix.$new_filename) )
			return array('success'=>true);
		else
			return array('success'=>false, 'error'=>"There was an error copying the file");
	}
	
	/**
	 * Resizes an image to fit the frontpage dimensions
	 * 
	 * @param string	filename
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function resizeForFrontpage($filename)
	{
		$file_type = $this->getFileType($this->prefix.$filename);
		
		if (!$file_type || $file_type != 'image')
			return array('success'=>false, 'error'=>"This file doesn't exist or isn't an image");
		
		$this->load->library('image_lib');
		
		list($width, $height) = getimagesize($this->prefix.$filename);
		$current_ratio = $width/$height;
		$target_width = 700;
		$target_height = 399;
		$target_ratio = $target_width/$target_height;
		$config['source_image'] = $this->prefix.$filename;
		
		if ($current_ratio > $target_ratio)
		{
			//resize first to height, maintain ratio
			$config['height'] = $target_height;
			$config['width'] = $target_height * $current_ratio;
			$this->image_lib->initialize($config);
			
			if (!$this->image_lib->resize())
				return array('success'=>false, 'error'=>"There was an error while resizing this image");
			
			//then crop off width
			$config['width'] = $target_width;
			$config['maintain_ratio'] = false;
			$this->image_lib->initialize($config);
			
			if ($this->image_lib->crop())
				return array('success'=>true);
			else
				return array('success'=>false, 'error'=>"There was an error while cropping this image");
		}
		else if ($current_ratio < $target_ratio)
		{
			//resize first to width, maintain ratio
			$config['width'] = $target_width;
			$config['height'] = $target_width / $current_ratio;
			$this->image_lib->initialize($config);
			
			if (!$this->image_lib->resize())
				return array('success'=>false, 'error'=>"There was an error while resizing this image");
			
			//then crop off height
			$config['height'] = $target_height;
			$config['maintain_ratio'] = false;
			$this->image_lib->initialize($config);
			
			if ($this->image_lib->crop())
				return array('success'=>true);
			else
				return array('success'=>false, 'error'=>"There was an error while cropping this image");
		}
		else {
			$config['width'] = $target_width;
			$config['height'] = $target_height;
			$this->image_lib->initialize($config);
			
			if ($this->image_lib->resize())
				return array('success'=>true);
			else
				return array('success'=>false, 'error'=>"There was an error while resizing this image");
		}
	}
}