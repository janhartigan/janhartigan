<script type="text/javascript">
var portfolioData = <?php echo $portfolio['success'] ? json_encode($portfolio['items']) : '[]'?>;
</script>
<div id="admin_content">
	<?php echo $this->load->view('admin_control_bar', '', true)?>
	<div id="portfolio_manager_container">
		<h2>Portfolio Manager</h2>
		<div class="admin_subtext">This is where you can manage the portfolio on the site.</div>
		
		<div id="portfolio_manager" class="admin_manager">
			<div id="portfolio_list_container">
				<table id="portfolio_list"></table>
			</div>
			<div id="add_portfolio_top" class="admin_button">Add Item</div>
			<div id="delete_portfolio_top" class="admin_button">Delete Selected</div>
			<div class="clear"></div>
			<div id="portfolio_item_area">
				<h4>Portfolio Details:</h4>
				<div id="active_items_list"></div>
				<div class="clear"></div>
				<div id="portfolio_item_details_left">
					<label for="portfolio_item_details_name">Name</label>
					<input type="text" id="portfolio_item_details_name" />
					
					<label for="portfolio_item_details_short_description">
						Short Description 
						<em>- characters left: <span class="character_count">600</span></em>
					</label>
					<textarea id="portfolio_item_details_short_description"></textarea>
					
					<label for="portfolio_item_details_uri">
						URI
					</label>
					<input type="text" id="portfolio_item_details_uri" />
					
					<label for="portfolio_item_details_live_url">
						Live URL
					</label>
					<input type="text" id="portfolio_item_details_live_url" />
					
					<div id="portfolio_item_details_published_area">
						<label>
							Published
						</label>
						
						<div>
							<label for="published_yes" class="small_label">Yes</label>
							<input type="radio" name="published" id="published_yes" value="yes" checked="checked" />
						</div>
						<div>
							<label for="published_no" class="small_label">No</label>
							<input type="radio" name="published" id="published_no" value="no" />
						</div>
					</div>
					
					<label for="portfolio_item_details_time" id="portfolio_item_details_time_label">
						Time
					</label>
					<input type="text" id="portfolio_item_details_time" />
				</div>
				<div id="portfolio_item_details_right">
					<label for="portfolio_item_details_image">
						Image URL (750x275px)
					</label>
					
					<input type="text" id="portfolio_item_details_image" readonly="readonly" />
					
					<div class="clear"></div>
					
					<label for="portfolio_item_details_image">
						Small Image URL (150x125px)
					</label>
					<input type="text" id="portfolio_item_details_image_small" readonly="readonly" />
				</div>
				<div class="clear"></div>
				
				<label for="portfolio_editor" class="item_editor_label">
					Description
				</label>
				<div id="portfolio_editor_container" class="yui-skin-sam clear">
					<textarea id="portfolio_editor" cols="50" rows="10"></textarea>
				</div>
				<div id="portfolio_editor_html">
					<textarea></textarea>
				</div>
				
				<div id="save_portfolio" class="admin_button">Save</div>
				<div id="save_and_close_portfolio" class="admin_button">Save And Close</div>
				<div id="html_portfolio_item" class="admin_button">View HTML</div>
				<div id="delete_portfolio" class="admin_button">Delete</div>
				<div id="close_portfolio" class="admin_button">Close</div>
				<div class="clear"></div>
			</div>
			<div id="portfolio_message"></div>
			<div class="clear"></div>
		</div>
	</div>
</div>