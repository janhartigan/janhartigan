<script type="text/javascript">
var creationsData = <?php echo $creations['success'] ? json_encode($creations['creations']) : '[]'?>;
</script>
<div id="admin_content">
	<?php echo $this->load->view('admin_control_bar', '', true)?>
	<div id="creations_manager_container">
		<h2>Creations Manager</h2>
		<div class="admin_subtext">This is where you can manage all creations on the site.</div>
		
		<div id="creations_manager" class="admin_manager">
			<div id="creations_list_container">
				<table id="creations_list"></table>
			</div>
			<div id="add_creation_top" class="admin_button">Add Creation</div>
			<div id="delete_creation_top" class="admin_button">Delete Selected</div>
			<div id="sync_github_creations" class="admin_button">Sync With Github</div>
			<div class="clear"></div>
			<div id="creation_item_area">
				<h4>Creation Details:</h4>
				<div id="active_items_list"></div>
				<div class="clear"></div>
				<div id="creation_item_details_left">
					<label for="creation_item_details_name">Name</label>
					<input type="text" id="creation_item_details_name" />
					
					<label for="creation_item_details_short_description">
						Short Description 
						<em>- characters left: <span class="character_count">600</span></em>
					</label>
					<textarea id="creation_item_details_short_description"></textarea>
					
					<label for="creation_item_details_uri">
						URI
					</label>
					<input type="text" id="creation_item_details_uri" />
					
					<label for="creation_item_details_uri">
						Github URL
					</label>
					<input type="text" id="creation_item_details_github_url" />
					
					<label for="creation_item_details_layout">
						Layout
					</label>
					<select id="creation_item_details_layout">
						<option>two-column</option>
						<option>full-width</option>
					</select>
					
					<label for="creation_item_details_tags_input">
						Tags
					</label>
					<input type="text" id="creation_item_details_tags_input" />
				</div>
				<div id="creation_item_details_right">
					<label for="creation_item_details_image">
						Image URL
					</label>
					
					<input type="text" id="creation_item_details_image" readonly="readonly" />
					
					<div class="clear"></div>
					
					<label for="creation_item_details_image">
						Small Image URL (150x125px)
					</label>
					<input type="text" id="creation_item_details_image_small" readonly="readonly" />
				</div>
				<div class="clear"></div>
				
				<label for="creation_editor">
					Description
				</label>
				<div id="creation_editor_container" class="yui-skin-sam clear">
					<textarea id="creation_editor" cols="50" rows="10"></textarea>
				</div>
				<div id="creation_editor_html">
					<textarea></textarea>
				</div>
				
				<div id="html_creation_item" class="admin_button">View HTML</div>
				
				<div class="clear"></div>
				
				<label for="creation_documentation_editor">
					Documentation
				</label>
				<div id="creation_documentation_editor_container" class="yui-skin-sam clear">
					<textarea id="creation_documentation_editor" cols="50" rows="10"></textarea>
				</div>
				<div id="creation_documentation_editor_html">
					<textarea></textarea>
				</div>
				
				<div id="save_creation" class="admin_button">Save</div>
				<div id="save_and_close_creation" class="admin_button">Save And Close</div>
				<div id="html_creation_documentation_item" class="admin_button">View HTML</div>
				<div id="delete_creation" class="admin_button">Delete</div>
				<div id="close_creation" class="admin_button">Close</div>
				<div class="clear"></div>
			</div>
			<div id="creation_message"></div>
			<div class="clear"></div>
		</div>
	</div>
</div>