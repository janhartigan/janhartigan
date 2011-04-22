<script type="text/javascript">
var filesData = <?php echo $files['success'] ? json_encode($files['files']) : '[]'?>;
</script>
<div id="admin_content">
	<?php echo $this->load->view('admin_control_bar', '', true)?>
	<div id="file_manager_container">
		<h2>File Manager</h2>
		<div class="admin_subtext">This is where you can manage all files on the site.</div>
		
		<div id="file_manager" class="admin_manager">
			<div id="file_list_container">
				<table id="file_list"></table>
			</div>
			<input type="button" id="delete_files" class="admin_button" value="Delete Selected" />
			<div id="file_upload_container">
				<strong>Upload file</strong>
				<br />
				<form id="file_upload_form" action="<?php echo base_url()?>upload/file" method="post">
					<input type="file" name="file" />
					<input type="submit" id="upload_file" value="Upload file" />
				</form>
				<div id="file_upload_message"></div>
			</div>
			<div class="clear"></div>
			
			<div id="active_files_area">
				<h4>Files:</h4>
				<div id="active_files_list">
					
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>