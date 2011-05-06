<script type="text/javascript">
var newsData = <?php echo $articles['success'] ? json_encode($articles['items']) : '[]'?>;
</script>
<div id="admin_content">
	<?php echo $this->load->view('admin_control_bar', '', true)?>
	<div id="news_manager_container">
		<h2>Articles Manager</h2>
		<div class="admin_subtext">This is where you can manage all articles on the site.</div>
		
		<div id="news_manager" class="admin_manager">
			<div id="news_list_container">
				<table id="news_list"></table>
			</div>
			<div id="add_news" class="admin_button">Add New Item</div>
			<div id="delete_news" class="admin_button">Delete Selected</div>
			<div class="clear"></div>
			<div id="news_item_area">
				<h4>Item Details:</h4>
				<div id="active_items_list"></div>
				<div class="clear"></div>
				<div id="news_item_details_left">
					<label for="news_item_details_title">
						Title <em>- characters left: <span class="character_count">40</span></em>
					</label>
					<input type="text" id="news_item_details_title" />
					
					<label for="news_item_details_description">
						Description <em>- characters left: <span class="character_count">150</span></em>
					</label>
					<textarea id="news_item_details_description"></textarea>
					
					<label for="news_item_details_title_url">
						URI Title
					</label>
					<input type="text" id="news_item_details_title_url" />
				</div>
				<div id="news_item_details_right">
					<label for="news_item_details_image">
						Image URL
					</label>
					<input type="text" id="news_item_details_image" />
					
					<div id="news_item_details_published_area">
						<label>
							Published
						</label>
						<br/>
						<label for="published_yes" class="small_label">Yes</label>
						<input type="radio" name="published" id="published_yes" value="yes" checked="checked" />
						<br />
						<label for="published_no" class="small_label">No</label>
						<input type="radio" name="published" id="published_no" value="no" />
					</div>
					
					<div id="news_item_details_date_area">
						<label>
							Date
						</label>
						<input type="text" id="news_item_details_date" />
					</div>
				</div>
				<div class="clear"></div>
				<label for="item_editor" class="item_editor_label">Content</label>
				<div id="item_editor_container" class="yui-skin-sam clear">
					<textarea id="item_editor" cols="50" rows="10"></textarea>
				</div>
				<div id="item_editor_html">
					<textarea></textarea>
				</div>
				<div id="save_news_item" class="admin_button">Save</div>
				<div id="save_and_close_news_item" class="admin_button">Save And Close</div>
				<div id="html_news_item" class="admin_button">View HTML</div>
				<div id="delete_news_item" class="admin_button">Delete</div>
				<div id="close_news_item" class="admin_button">Close</div>
				<div class="clear"></div>
			</div>
			<div id="news_message"></div>
			<div class="clear"></div>
		</div>
	</div>
</div>