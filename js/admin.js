$(document).ready(function() {
	if (typeof(newsData) != 'undefined')
		news.initiate();
	
	if (typeof(filesData) != 'undefined')
		files.initiate();
	
	if (typeof(creationsData) != 'undefined')
		creations.initiate();
	
	if (typeof(portfolioData) != 'undefined')
		portfolio.initiate();
});

/*///////////////////////////////////////////////////////////////
 * NEWS SECTION
 *///////////////////////////////////////////////////////////////

/******************
 * news object
 */
(function() {
	function news(){};
	
	news.prototype = {
		//object of selected item details (and new item)...included is 'edited' key..set to true if user enters an input box
		itemDetails: {},
		//active item in the item details area by id (0 if new...new is exclusive details box...can't do multiple)
		activeItem: 0,
		//data template for new items
		blankItem: {
			id: 0,
			title: '',
			description: '',
			date: '',
			content: '',
			published: true
		},
		//array of selected item ids; for solving the sort deselect bug in jqgrid
		selectedItemIds: [],
		
		/**
		 * Initiates the news manager page
		 */
		initiate: function() {
			var self = this;
			
			this.initiateAdminGrid();
			this.initiateNewsTextEditor();
			characters.addField($('#news_item_details_title'), 200);
			characters.addField($('#news_item_details_description'), 300);
			$('#news_item_details_date').datepicker({ dateFormat: 'yy-mm-dd' });
			
			//open new news item form
			$('#add_news').click(function() {
				self.toggleAddItem();
			});
			
			//save news item
			$('#save_news_item').click(function() {
				self.saveItem();
			});
			//save and close news item
			$('#save_and_close_news_item').click(function() {
				self.saveItem(true);
			});
			//close news item
			$('#close_news_item').click(function() {
				self.closeItem();
			});
			//delete news item
			$('#delete_news_item').click(function() {
				self.deleteItem();
			});
			//toggle news item html
			$('#html_news_item').click(function() {
				self.toggleEditorHtml();
			});
			
			//sets up news item fields to be validated on change
			var selector = '#news_item_details_title, #news_item_details_image, #news_item_details_description, #news_item_details_date' +
							'#item_editor_html textarea';
			$(selector).change(function() {
				self.fieldChange();
			});
			
			//active items list click event
			$('#active_items_list > div').live('click', function() {
				self.activeItem = parseInt($('input[name=id]', $(this)).val().substr(2));
				self.checkItemData();
				self.renderActiveItem();
			});
			
			//set up the syntax highlighter
			$.SyntaxHighlighter.init({
				lineNumbers : false,
				prettifyBaseUrl : ''
			});
			
			//set up the directoryTree plugin for the image picker
			$('#news_item_details_image').filePicker({
				dataSource		: '/admin/getdirectory',
				baseDirectory	: '/images',
				afterSelectFile : function(data) {
					console.log(data);
				}
			});
		},
		
		/**
		 * Initiates the news admin datagrid
		 */
		initiateAdminGrid: function() {
			var self = this,
				i = 0,
				newsLength = newsData.length;
			
			$('#news_list').jqGrid({
				datatype: "local",
				height: 200,
				colNames: ['Date', 'Title', 'Description', 'Published', 'ID'],
				colModel: [{name:'date', index:'date', width:80, sorttype:'text', align:'center'},
				           {name:'title', index:'title', width:200, sorttype:'text'},
				           {name:'description', index:'description', width:400, sorttype:'text'},
				           {name:'published', index:'published', width:80, sorttype:'text', align:'center'},
				           {name:'id', index:'id', width:40, sorttype:'int', align:'center'}],
				multiselect: true,
				caption: "News Items",
				//events
				onSelectRow: function(id, status) {
					self.selectRow(id, status, true);
				},
				onSelectAll: function(aIds, status) {
					//loop through rows, then render afterward
					$.each(aIds, function(ind, el) {
						self.selectRow(el, status, false)
					});
					self.checkItemData();
					self.renderActiveItem();
				},
				gridComplete: function() {
					self.setSelectedRows();
				}
			});
			
			for(i; i<=newsLength; i++)
				$("#news_list").jqGrid('addRowData',i+1,newsData[i]);
		},
		
		/**
		 * Handles a row selection on the news table
		 * 
		 * @param int	rowId
		 * @param bool	status
		 * @param bool	render = whether or not to render the data after set (false for select all)
		 */
		selectRow: function(rowId, status, render) {
			var itemId = parseInt($('#news_list').jqGrid('getRowData', rowId)['id']),
				selectedIndex = $.inArray(itemId, this.selectedItemIds);
			
			//fixes how it returns header row in select all
			if (typeof(rowId) == "undefined")
				return false;
			
			if (status) {
				if (selectedIndex == -1) {
					this.selectedItemIds.push(itemId);
					this.getDetails(itemId);
				} else {
					this.activeItem = itemId;
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			} else {
				if (selectedIndex != -1) {
					this.selectedItemIds.splice(selectedIndex, 1);
					delete this.itemDetails['id'+itemId];
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			}
		},
		
		/**
		 * Reselects all selected rows in the jqgrid after sorting or reloading the table data
		 */
		setSelectedRows: function() {
			$.each(this.selectedItemIds, function(ind, el) {
				var rowId = $('td[aria-describedby=news_list_id][title='+el+']').parent().attr('id');
				$("#news_list").jqGrid('setSelection', rowId, false);
			});
		},
		
		/**
		 * Initiates the wysiwyg editor on the news admin page
		 */
		initiateNewsTextEditor: function() {
			myEditor = new YAHOO.widget.Editor('item_editor', {
			    height: '300px',
			    width: '100%',
			    dompath: true, //Turns on the bar at the bottom
			    animate: true //Animates the opening, closing and moving of Editor windows
			});
			myEditor.render();
		},
		
		/**
		 * gets the length of the itemDetails object
		 */
		getItemDetailsLength: function() {
			var count = 0;
			
			$.each(this.itemDetails, function(ind, el) {
				count++;
			});
			
			return count;
		},
		
		/**
		 * Toggles the add news
		 */
		toggleAddItem: function() {
			//TODO: add check to see if unsaved changes
			var item_area = $('#news_item_area');
		
			if (typeof(this.itemDetails['id0']) == "undefined") {
				if (this.getItemDetailsLength() > 0)
					this.storeNewsItemFields();
				
				this.activeItem = 0;
				this.itemDetails['id0'] = this.blankItem;
				this.checkItemData();
				this.renderActiveItem();
			} else {
				delete this.itemDetails['id0'];
				this.checkItemData();
				this.renderActiveItem();
			}
		},
		
		/**
		 * Sets presentation state based on the items in the itemDetails array 
		 */
		checkItemData: function () {
			var activeList = '',
				activeFound = false
				activeIdArr = [],
				self = this;
			
			//if there aren't any items at all
			if (this.getItemDetailsLength() == 0) {
				this.clearNewsItemFields();
				$('#add_news').text("Add New Item");
				$('#news_item_area').hide();
				$('#active_items_list').html('');
				return false;
			}
			
			$('#news_item_area').show();
			
			//if there isn't a "new" item slot
			if (typeof(this.itemDetails['id0']) == "undefined") {
				$('#add_news').text("Add New Item");
			} else {
				$('#add_news').text("Cancel Add");
			}
			
			$.each(this.itemDetails, function(ind, el) {
				var activeClass = '',
					title = $.trim(el.title) == '' ? 'New Item' : el.title;
				
				if (ind.substr(2) == self.activeItem) {
					activeFound = true;
					activeClass += 'active_item_tag ';
				}
				
				if (el.edited) 
					activeClass += 'editing_item_tag';
				
				if (activeClass.length) 
					activeClass = ' class="' + activeClass + '"';
				
				activeIdArr.push(ind.substr(2));
				activeList += '<div'+activeClass+'>'+title+'<input type="hidden" name="id" value="'+ind+'" /></div>';
			});
			
			$('#active_items_list').html(activeList);
			
			if (!activeFound)
				this.activeItem = activeIdArr[0];
		},
		
		/**
		 * Renders the active item
		 * 
		 * @return bool
		 */
		renderActiveItem: function() {
			if (typeof(this.itemDetails['id'+this.activeItem]) == "undefined")
				return false;
			
			var item = this.itemDetails['id'+this.activeItem];
			
			$('#news_item_details_title').val(item.title).removeClass('unvalidated').trigger('keyup');
			$('#news_item_details_title_url').val(item.title_url).removeClass('unvalidated').trigger('keyup');
			$('#news_item_details_description').val(item.description).removeClass('unvalidated').trigger('keyup');
			$('#news_item_details_image').val(item.image).removeClass('unvalidated');
			$('#news_item_details_date').val(item.date).removeClass('unvalidated');
			myEditor.setEditorHTML(item.content);
			$('#item_editor_html textarea').val(item.content);
			
			if (parseInt(item.published))
				$('#published_yes').trigger('click')
			else
				$('#published_no').trigger('click')
			
			$('#active_items_list input[value=id' + this.activeItem + ']').parent().addClass('active_item_tag');
			
			return true;
		},
		
		/**
		 * Handles getting a news item on row click
		 */
		getDetails: function(id) {
			var self = this;
			
			$.post('/admin/getnewsitem/'+id, '', function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (data.success) {
					self.itemDetails['id'+data.item.id] = data.item;
					self.activeItem = data.item.id;
					
					self.checkItemData();
					self.renderActiveItem();
				} else {
					alert(data.error);
				}
			}, 'text');
		},
		
		/**
		 * Clears the news item fields
		 */
		clearNewsItemFields: function() {
			$('#news_item_details_title').val('').removeClass('unvalidated');
			$('#news_item_details_title_url').val('').removeClass('unvalidated');
			$('#news_item_details_description').val('').removeClass('unvalidated');
			$('#news_item_details_image').val('').removeClass('unvalidated');
			$('#published_yes').trigger('click')
			$('#news_item_details_date').val('').removeClass('unvalidated');
			myEditor.clearEditorDoc();
		},
		
		/**
		 * Stores the news item fields
		 */
		storeNewsItemFields: function() {
			this.itemDetails['id'+this.activeItem] = {
					title: $('#news_item_details_title').val(),
					title_url: $('#news_item_details_title_url').val(),
					description: $('#news_item_details_description').val(),
					image: $('#news_item_details_image').val(),
					date: $('#news_item_details_date').val(),
					published: $('input[name=published]:checked').val(),
					content: $('#item_editor_container').is(':visible') 
								? myEditor.getEditorHTML() 
								: $('#item_editor_html textarea').val()
			}
		},
		
		/**
		 * Stores the news item fields
		 * 
		 * @return bool		false if one or more are invalid
		 */
		validateNewsItemFields: function() {
			var fields = ['news_item_details_title', 
			              'news_item_details_description', 
			              'news_item_details_date'],
			    valid = true;
			
			$.each(fields, function(ind, el) {
				if ($('#'+el).val() == '') {
					$('#'+el).addClass('unvalidated');
					valid = false;
				} else
					$('#'+el).removeClass('unvalidated');
			});
			
			return valid;
		},
		
		/**
		 * Handles changes in any of the fields
		 */
		fieldChange: function() {
			this.validateNewsItemFields();
			this.storeNewsItemFields();
			this.setActiveToEdited();
		},
		
		/**
		 * Sets the active item to edited
		 */
		setActiveToEdited: function() {
			this.itemDetails['id'+this.activeItem].edited = true;
			this.checkItemData();
		},
		
		/**
		 * Saves a news item
		 */
		saveItem: function(close) {
			var data = {
					title: $('#news_item_details_title').val(),
					title_url: $('#news_item_details_title_url').val(),
					description: $('#news_item_details_description').val(),
					image: $('#news_item_details_image').val(),
					published: $('input[name=published]:checked').val(),
					date: $('#news_item_details_date').val(),
					content: $('#item_editor_container').is(':visible') 
										? myEditor.getEditorHTML() 
										: $('#item_editor_html textarea').val(),
					id: this.activeItem
				},
				post = {},
				self = this;
			
			if (!this.validateNewsItemFields()) {
				alert("Please enter values for all fields");
				return false;
			}
			
			data.marked_up_content = this.prepareItemContent(data.content);
			
			post = {data: $.toJSON(data)};
			
			raiseNewsMessage("Saving...", '', false);
			
			$.post('/admin/savenewsitem', post, function(textdata) {
				var save = $.evalJSON(textdata);
				
				if (save.success) {
					raiseNewsMessage("Saved!", 'success');
					
					if (!parseInt(data.id)) {
						delete self.itemDetails['id0'];
					}
					
					if (typeof(close) != 'undefined' && close) {
						selectedIndex = $.inArray(parseInt(save.item.id), self.selectedItemIds);
						
						if (selectedIndex != -1) {
							self.selectedItemIds.splice(selectedIndex, 1);
						}
						
						delete self.itemDetails['id'+save.item.id];
					} else {
						self.activeItem = parseInt(save.item.id);
						self.itemDetails['id'+save.item.id] = save.item;
					}
					self.checkItemData();
					self.renderActiveItem();
					self.getNewsTableData();
				} else {
					raiseNewsMessage(save.error, 'error');
				}
			}, 'text');
		},
		
		/**
		 * Prepares the news item content by stripping certain tags and highlighting code
		 * 
		 * @param {string}	content
		 * 
		 * @return {string}
		 */
		prepareItemContent: function(content) {
			var $content = null;
			
			content = content.replace(/(<.??font.*?>)/ig,"");
			$content = $('<div>' + content + '</div>');
			
			$('body').append($content);
			
			$content.syntaxHighlight();
			content = $content.html();
			$content.remove();
			
			return content;
		},
		
		/**
		 * Deletes a news item
		 */
		deleteItem: function() {
			var data = new Object(),
				self = this,
				conf = confirm("Are you sure you want to permanently delete this news item?");
			
			if (!conf)
				return false;
			
			data.id = this.activeItem;
			
			if (data.id == 0) {
				raiseNewsMessage("This item has not yet been created", 'error');
			}
			
			var post = new Object();
			post.data = $.toJSON(data);
			
			raiseNewsMessage("Deleting...", '', false);
			
			$.post('/admin/deletenewsitem', post, function(textdata) {
				var deleted = $.evalJSON(textdata);
				
				if (deleted.success) {
					raiseNewsMessage("Deleted!", 'success');
					
					selectedIndex = $.inArray(parseInt(data.id), self.selectedItemIds);
						
					if (selectedIndex != -1) {
						self.selectedItemIds.splice(selectedIndex, 1);
					}
						
					delete self.itemDetails['id'+data.id];
					
					self.checkItemData();
					self.renderActiveItem();
					self.getNewsTableData();
				} else {
					raiseNewsMessage(save.error, 'error');
				}
			}, 'text');
		},
		
		/**
		 * Closes an item 
		 */
		closeItem: function() {
			selectedIndex = $.inArray(parseInt(this.activeItem), this.selectedItemIds);
			
			if (selectedIndex != -1) {
				this.selectedItemIds.splice(selectedIndex, 1);
			}
			
			delete self.itemDetails['id'+this.activeItem];
			self.checkItemData();
			self.renderActiveItem();
			self.getNewsTableData();
		},
		
		/**
		 * Gets and repopulates the data in the news item table
		 */
		getNewsTableData: function() {
			var self = this;
			
			$.post('/admin/getnewsitems', '', function(textdata) {
				var items = $.evalJSON(textdata),
					newsLength = 0,
					i;
				
				if (items.success) {
					newsData = items.items;
				} else {
					newsData = [];
				}
				
				$('#news_list').jqGrid('clearGridData');
				newsLength = newsData.length;
				
				for(i = 0; i<=newsLength; i++)
					$("#news_list").jqGrid('addRowData',i+1,newsData[i]);
				
				self.setSelectedRows();
			}, 'text');
		},
		
		toggleEditorHtml: function() {
			if ($('#html_news_item').text() == 'View HTML') {
				var content = myEditor.getEditorHTML();
				
				$('#item_editor_html').show();
				$('#item_editor_container').hide();
				$('#item_editor_html textarea').val(content);
				
				$('#html_news_item').text('View Editor');
			} else {
				var content = $('#item_editor_html textarea').val();;
				
				$('#item_editor_html').hide();
				$('#item_editor_container').show();
				myEditor.setEditorHTML(content);
				
				$('#html_news_item').text('View HTML');
			}
		}
	};
	
	/**
	 * Raises a message (either neutral, error, or success type) and either fades it away after a few seconds or it doesn't
	 * 
	 * @param string	message	<= string to display
	 * @param string	type <= class to set the message to. 'error', 'success', or null/''
	 * @param bool		fade <= true/null to have it fade, false to let it stay indefinitely
	 */
	function raiseNewsMessage(message, type, fade) {
		if (typeof(type) == 'undefined')
			type = '';
		
		$('#news_message').attr('class', type).show().text(message);
		
		if (typeof(fade) != 'undefined' && !fade) {
			setTimeout(function() {
				$('#news_message').hide();
			}, 3000);
		}
	}
	
	window.news = new news();
})();

/**
 * characters left object
 */
(function() {
	function characters(){};
	
	characters.prototype = {
		//list of character field objects 
		characterCounters: [],
		
		/**
		 * Adds a new field to the character checker
		 * 
		 * @param jQueryObject		obj - item to be added
		 * @param int				limit - character limit
		 */
		addField: function(obj, limit) {
			var self = this,
				counter = null,
				id = $(obj).attr('id');
			
			//find label
			$('label').each(function(ind, el) {
				if ($(el).attr('for') == id) {
					counter = $('.character_count', el);
					return;
				}
			});
			
			this.characterCounters[id] = {
					counter: counter,
					limit: limit
			};
			
			$(obj).keyup(function(e) {
				var origVal = $(this).val();
				
				//set to substr on max
				if (self.characterCounters[id].limit - origVal.length < 0) {
					$(this).val(origVal.substr(0, self.characterCounters[id].limit));
				}
				//change label
				$(self.characterCounters[id].counter).text(self.characterCounters[id].limit - $(this).val().length)
			});
		}
	};
	
	window.characters = new characters();
})();

/*///////////////////////////////////////////////////////////////
 * FILES SECTION
 *///////////////////////////////////////////////////////////////

/******************
 * files object
 */
(function() {
	function files(){};
	
	files.prototype = {
		//array of selected item ids; for solving the sort deselect bug in jqgrid
		selectedFileURLs: [],
		
		/**
		 * Dom ready initiator for files page
		 */
		initiate: function() {
			var self = this;
			
			this.initiateFilesGrid();
			
			$('#file_upload_form').submit(function() {
				self.submitFile(); return false;
			});
			$('#delete_files').click(function(){
				self.deleteSelectedFiles();
			});
			
			//file details list
			$('.close_file').live('click', function() {
				var url = $('.url_spot', $(this).parent()).text(),
					rowId = $('td[aria-describedby=file_list_url][title='+url+']').parent().attr('id');
				$("#file_list").jqGrid('setSelection', rowId);
			});
			$('.delete_file').live('click', function() {
				self.deleteFile($('.url_spot', $(this).parent()).text());
			});
			$('.make_copy').live('click', function() {
				self.copyFile($('.url_spot', $(this).parent()).text());
			});
			$('.resize_and_crop').live('click', function() {
				var img = $('img', $(this).parent());
				self.resizeFile($('.url_spot', $(this).parent()).text(), img);
			});
		},
		
		/**
		 * Initiates the files admin datagrid
		 */
		initiateFilesGrid: function() {
			var self = this,
				i = 0,
				filesLength = filesData.length;
			
			$('#file_list').jqGrid({
				datatype: "local",
				height: 250,
				colNames: ['Name', 'Type', 'Date', 'Size (kb)', 'URL'],
				colModel: [{name:'name', index:'name', width:200, sorttype:'text'},
				           {name:'type', index:'type', width:60, sorttype:'text', align:'center'},
				           {name:'date', index:'date', width:80, sorttype:'text', align:'center'},
				           {name:'size', index:'size', width:80, sorttype:'number', align:'right'},
				           {name:'url', index:'url', width:300, sorttype:'text'}],
				multiselect: true,
				caption: "Uploaded Files",
				//events
				onSelectRow: function(id, status) {
					self.selectRow(id, status, true);
				},
				onSelectAll: function(aIds, status) {
					//loop through rows, then render afterward
					$.each(aIds, function(ind, el) {
						self.selectRow(el, status, false)
					});
					self.renderSelectedFiles();
				},
				gridComplete: function() {
					self.setSelectedRows();
				}
			});
			
			$("#file_list").jqGrid('addRowData','rowId',filesData);
		},
		
		/**
		 * Handles a row selection on the files table
		 * 
		 * @param int	rowId
		 * @param bool	status
		 * @param bool	render = whether or not to render the data after set (false for select all)
		 */
		selectRow: function(rowId, status, render) {
			var itemURL = $('#file_list').jqGrid('getRowData', rowId)['url'],
				selectedIndex = $.inArray(itemURL, this.selectedFileURLs);
			
			//fixes how it returns header row in select all
			if (typeof(rowId) == "undefined")
				return false;
			
			if (status) {
				if (selectedIndex == -1) {
					this.selectedFileURLs.push(itemURL);
					
					if (render)
						this.renderSelectedFiles();
				}
			} else {
				if (selectedIndex != -1) {
					this.selectedFileURLs.splice(selectedIndex, 1);
					
					if (render)
						this.renderSelectedFiles();
				}
			}
		},
		
		/**
		 * Reselects all selected rows in the jqgrid after sorting or reloading the table data
		 */
		setSelectedRows: function() {
			$.each(this.selectedFileURLs, function(ind, el) {
				var rowId = $('td[aria-describedby=file_list_url][title='+el+']').parent().attr('id');
				$("#file_list").jqGrid('setSelection', rowId, false);
			});
		},
		
		/**
		 * Renders the selected file list 
		 */
		renderSelectedFiles: function() {
			var list = '';
			
			$.each(this.selectedFileURLs, function(ind, el) {
				var rowId = $('td[aria-describedby=file_list_url][title='+el+']').parent().attr('id'),
					row = $("#file_list").jqGrid('getRowData', rowId);
				
				if (row.size) {
					list += '<div class="active_file">' +
								'<div class="url_spot">'+row.url+'</div>' +
								'<input type="button" class="make_copy admin_button" value="Make Copy" />' +
								'<input type="button" class="resize_and_crop admin_button" value="Resize and Crop for Frontpage" />' +
								'<image src="'+row.url+'" />' +
								'<input type="button" class="close_file admin_button" value="Close" />' +
								'<input type="button" class="delete_file admin_button" value="Delete" />' +
								'<div class="clear"></div>' +
							'</div>';
				}
			});
			
			if (list == '')
				list = '<div><em>No files selected</em></div>'
			
			$('#active_files_list').empty();
			$('#active_files_list').html(list);
		},
		
		/**
		 * Submits a file asynchronously through the jquery form plugin
		 */
		submitFile: function() {
			var self = this;
			
			if ($('#file_upload_form input[type=file]').val() == '')
				return raiseFileMessage("Please select a file", 'error');
			
			raiseFileMessage("Uploading files...", '', false);
			
			$('#file_upload_form').ajaxSubmit({
				success: function(text) {
					var data = $.evalJSON($(text).html());
					
					$('#file_upload_form input[type=file]').val('');
					raiseFileMessage("Your file has been uploaded", 'success');
					self.getFilesTableData();
				}
			});
		},
		
		/**
		 * Deletes a file
		 */
		deleteFile: function(url) {
			var self = this,
			selectedFiles = [],
			post = new Object(),
			conf = null,
			
			conf = confirm("Are you sure you want to permanently delete this file?");
			
			if (!conf)
				return false;
			
			spliturl = url.split('/');
			selectedFiles.push(spliturl[spliturl.length-1]); 
			
			post.data = $.toJSON({files: selectedFiles});
			
			$.post('/admin/deletefiles', post, function(textdata) {
				var data = $.evalJSON(textdata),
					selectedIndex = null;
				
				if (!data.success) {
					alert(data.error);
				}
				
				self.getFilesTableData();
				selectedIndex = $.inArray(url, self.selectedFileURLs);
				
				if (selectedIndex != -1)
					self.selectedFileURLs.splice(selectedIndex, 1);
				
				self.renderSelectedFiles();
			}, 'text');
		},
		
		/**
		 * Deletes selected files
		 */
		deleteSelectedFiles: function() {
			var self = this,
				selectedRows = $("#file_list").getGridParam('selarrrow'),
				selectedFiles = [],
				selectedURLs = [],
				post = new Object(),
				numRows = selectedRows.length,
				conf = null,
				confText = '';
			
			if (!numRows) {
				alert("Please select some files");
				return false;
			}
			
			confText = "Are you sure you want to permanently delete " + (numRows > 1 ? ("these " + numRows + " files?") : "this file?");
			conf = confirm(confText);
			
			if (!conf)
				return false;
			
			$.each(selectedRows, function(ind, el) {
				var row = $('#file_list').getRowData(el);
				selectedFiles.push(row.name);
				selectedURLs.push(row.url);
			});
			
			post.data = $.toJSON({files: selectedFiles});
			
			$.post('/admin/deletefiles', post, function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (data.success) {
					$.each(selectedURLs, function(ind, el) {
						selectedIndex = $.inArray(el, self.selectedFileURLs);
						
						if (selectedIndex != -1)
							self.selectedFileURLs.splice(selectedIndex, 1);
					});
				} else {
					alert(data.error);
				}
				
				self.getFilesTableData();
				self.renderSelectedFiles();
			}, 'text');
		},
		
		/**
		 * Copies a file
		 */
		copyFile: function(url) {
			var self = this,
			selectedFile = '',
			post = new Object(),
			conf = null,
			
			conf = confirm("Are you sure you want to make a copy of this file?");
			
			if (!conf)
				return false;
			
			spliturl = url.split('/');
			selectedFile = spliturl[spliturl.length-1]; 
			
			post.data = $.toJSON({filename: selectedFile});
			
			$.post('/admin/copyfile', post, function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (!data.success) {
					alert(data.error);
				}
				
				self.getFilesTableData();
			}, 'text');
		},
		
		/**
		 * Resizes a file
		 */
		resizeFile: function(url, img) {
			var self = this,
			selectedFile = '',
			post = new Object(),
			conf = null,
			conf = confirm("Are you sure you want to permanently resize this file to fit the front page dimensions?");
			
			if (!conf)
				return false;
			
			spliturl = url.split('/');
			selectedFile = spliturl[spliturl.length-1]; 
			
			post.data = $.toJSON({filename: selectedFile});
			
			$.post('/admin/resizefile', post, function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (!data.success) {
					alert(data.error);
				} else {
					var timestamp = new Date().getTime();
					var src = $(img).attr('src') + "?" + timestamp;
					
					$(img).attr('src', src);
				}
				
				self.getFilesTableData();
			}, 'text');
		},
		
		/**
		 * Gets and repopulates the data in the files table
		 */
		getFilesTableData: function() {
			var self = this;
			
			$.post('/admin/getfiles', '', function(textdata) {
				var files = $.evalJSON(textdata),
					filesLength = 0,
					i = 0;
				
				if (files.success) {
					filesData = files.files;
				} else {
					filesData = [];
				}
				
				$('#file_list').jqGrid('clearGridData');
				$("#file_list").jqGrid('addRowData','rowId',filesData);
			}, 'text');
		}
	};
	
	/**
	 * Raises a message (either neutral, error, or success type) and either fades it away after a few seconds or it doesn't
	 * 
	 * @param string	message	<= string to display
	 * @param string	type <= class to set the message to. 'error', 'success', or null/''
	 * @param bool		fade <= true/null to have it fade, false to let it stay indefinitely
	 */
	function raiseFileMessage(message, type, fade) {
		if (typeof(type) == 'undefined')
			type = '';
		
		$('#file_upload_message').text(message).attr('class', type).show();
		
		if (typeof(fade) != 'undefined' && !fade) {
			setTimeout(function() {
				$('#file_upload_message').hide();
			}, 3000);
		}
	}
	
	window.files = new files();
})();



/*///////////////////////////////////////////////////////////////
 * CREATIONS SECTION
 *///////////////////////////////////////////////////////////////

/******************
 * creations object
 */
(function() {
	function creations(){};
	
	creations.prototype = {
		//object of selected item details (and new item)...included is 'edited' key..set to true if user enters an input box
		itemDetails: {},
		//active item in the item details area by id (0 if new...new is exclusive details box...can't do multiple)
		activeItem: 0,
		//data template for new items
		blankItem: {
			id: 0,
			name: '',
			short_description: '',
			description: '',
			uri: true,
			image: ''
		},
		//array of selected item ids; for solving the sort deselect bug in jqgrid
		selectedItemIds: [],
		
		//image preview scaffold for the image inputs
		image_preview: ['<div class="image_preview"><input type="hidden" value="', 
						'" /><img src="',
						'" /><div class="image_preview_x">X</div></div>'],
		
		/* the description editor
		 * yahoo editor
		 */
		descriptionEditor: null,
		
		/* the documentation editor
		 * yahoo editor
		 */
		docsEditor: null,
		
		/**
		 * Initiates the creations manager page
		 */
		initiate: function() {
			var self = this;
			
			this.initiateAdminGrid();
			this.initiateCreationsTextEditor();
			characters.addField($('#creation_item_details_short_description'), 600);
			
			//open new creation item form
			$('#add_creation_top').click(function() {
				self.toggleAddItem();
			});
			
			//creations github sync
			$('#sync_github_creations').click(function() {
				self.syncWithGitHub();
			});
			
			//save creation
			$('#save_creation').click(function() {
				self.saveItem();
			});
			//save and close creation
			$('#save_and_close_creation').click(function() {
				self.saveItem(true);
			});
			//close creation
			$('#close_creation').click(function() {
				self.closeItem();
			});
			//delete creation
			$('#delete_creation').click(function() {
				self.deleteItem();
			});
			//toggle creation description html
			$('#html_creation_item').click(function() {
				self.toggleEditorHtml();
			});
			//toggle creation documentation html
			$('#html_creation_documentation_item').click(function() {
				self.toggleDocsEditorHtml();
			});
			
			//sets up creation fields to be validated on change
			var selector = '#creation_item_details_name, #creation_item_details_short_description';
			$(selector).change(function() {
				self.fieldChange();
			});
			
			//active items list click event
			$('#active_items_list > div').live('click', function() {
				self.activeItem = parseInt($('input[name=id]', $(this)).val().substr(2));
				self.checkItemData();
				self.renderActiveItem();
			});
			
			//set up the syntax highlighter
			$.SyntaxHighlighter.init({
				lineNumbers : false,
				prettifyBaseUrl : ''
			});
			
			//set up the filePicker plugin for the image fields
			$('#creation_item_details_image, #creation_item_details_image_small').filePicker({
				dataSource		: '/admin/getdirectory',
				baseDirectory	: '/images',
				afterSelectFile : function(data) {
					this.val(data.path);
					
					this.parent().find('input[value="' + this.attr('id') + '"]').parent().remove();
					this.after(self.image_preview[0] + this.attr('id') + self.image_preview[1] + data.path + self.image_preview[2]);
				}
			});
			
			$('#creation_item_area').delegate('.image_preview_x', 'click', function() {
				var $this = $(this),
					id = $this.siblings('input').val(),
					conf = confirm("Are you sure you want to remove this image?");
				
				if (!conf) return false;
				
				$this.parent().remove();
				$('#'+id).val('');
			});
		},
		
		/**
		 * Initiates the creations admin datagrid
		 */
		initiateAdminGrid: function() {
			var self = this,
				i = 0,
				creationsLength = creationsData.length;
			
			$('#creations_list').jqGrid({
				datatype: "local",
				height: 200,
				colNames: ['Time', 'Name', 'Short Description', 'ID'],
				colModel: [{name:'time', index:'time', width:120, sorttype:'text', align:'center'},
				           {name:'name', index:'name', width:180, sorttype:'text'},
				           {name:'short_description', index:'short_description', width:460, sorttype:'text'},
				           {name:'id', index:'id', width:40, sorttype:'int', align:'center'}],
				multiselect: true,
				caption: "Creations",
				//events
				onSelectRow: function(id, status) {
					self.selectRow(id, status, true);
				},
				onSelectAll: function(aIds, status) {
					//loop through rows, then render afterward
					$.each(aIds, function(ind, el) {
						self.selectRow(el, status, false)
					});
					self.checkItemData();
					self.renderActiveItem();
				},
				gridComplete: function() {
					self.setSelectedRows();
				}
			});
			
			for(i; i<=creationsLength; i++)
				$("#creations_list").jqGrid('addRowData',i+1,creationsData[i]);
		},
		
		/**
		 * Handles a row selection on the creations table
		 * 
		 * @param int	rowId
		 * @param bool	status
		 * @param bool	render = whether or not to render the data after set (false for select all)
		 */
		selectRow: function(rowId, status, render) {
			var itemId = parseInt($('#creations_list').jqGrid('getRowData', rowId)['id']),
				selectedIndex = $.inArray(itemId, this.selectedItemIds);
			
			//fixes how it returns header row in select all
			if (typeof(rowId) == "undefined")
				return false;
			
			if (status) {
				if (selectedIndex == -1) {
					this.selectedItemIds.push(itemId);
					this.getDetails(itemId);
				} else {
					this.activeItem = itemId;
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			} else {
				if (selectedIndex != -1) {
					this.selectedItemIds.splice(selectedIndex, 1);
					delete this.itemDetails['id'+itemId];
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			}
		},
		
		/**
		 * Reselects all selected rows in the jqgrid after sorting or reloading the table data
		 */
		setSelectedRows: function() {
			$.each(this.selectedItemIds, function(ind, el) {
				var rowId = $('td[aria-describedby=creations_list_id][title='+el+']').parent().attr('id');
				$("#creations_list").jqGrid('setSelection', rowId, false);
			});
		},
		
		/**
		 * Initiates the wysiwyg editor on the creations admin page
		 */
		initiateCreationsTextEditor: function() {
			this.descriptionEditor = new YAHOO.widget.Editor('creation_editor', {
			    height: '150px',
			    width: '100%',
			    dompath: true, //Turns on the bar at the bottom
			    animate: true //Animates the opening, closing and moving of Editor windows
			});
			this.descriptionEditor.render();
			
			this.docsEditor = new YAHOO.widget.Editor('creation_documentation_editor', {
			    height: '300px',
			    width: '100%',
			    dompath: true, //Turns on the bar at the bottom
			    animate: true //Animates the opening, closing and moving of Editor windows
			});
			this.docsEditor.render();
		},
		
		/**
		 * gets the length of the itemDetails object
		 */
		getItemDetailsLength: function() {
			var count = 0;
			
			$.each(this.itemDetails, function(ind, el) {
				count++;
			});
			
			return count;
		},
		
		/**
		 * Toggles the add creation
		 */
		toggleAddItem: function() {
			//TODO: add check to see if unsaved changes
			var item_area = $('#creation_item_area');
		
			if (typeof(this.itemDetails['id0']) == "undefined") {
				if (this.getItemDetailsLength() > 0)
					this.storeCreationItemFields();
				
				this.activeItem = 0;
				this.itemDetails['id0'] = this.blankItem;
				this.checkItemData();
				this.renderActiveItem();
			} else {
				delete this.itemDetails['id0'];
				this.checkItemData();
				this.renderActiveItem();
			}
		},
		
		/**
		 * Sets presentation state based on the items in the itemDetails array 
		 */
		checkItemData: function () {
			var activeList = '',
				activeFound = false
				activeIdArr = [],
				self = this;
			
			//if there aren't any items at all
			if (this.getItemDetailsLength() == 0) {
				this.clearCreationItemFields();
				$('#add_creation_top').text("Add New Item");
				$('#creation_item_area').hide();
				$('#active_items_list').html('');
				return false;
			}
			
			$('#creation_item_area').show();
			
			//if there isn't a "new" item slot
			if (typeof(this.itemDetails['id0']) == "undefined")
				$('#add_creation_top').text("Add New Item");
			else
				$('#add_creation_top').text("Cancel Add");
			
			$.each(this.itemDetails, function(ind, el) {
				var activeClass = '',
					name = $.trim(el.name) == '' ? 'New Item' : el.name;
				
				if (ind.substr(2) == self.activeItem) {
					activeFound = true;
					activeClass += 'active_item_tag ';
				}
				
				if (el.edited) 
					activeClass += 'editing_item_tag';
				
				if (activeClass.length) 
					activeClass = ' class="' + activeClass + '"';
				
				activeIdArr.push(ind.substr(2));
				activeList += '<div'+activeClass+'>'+name+'<input type="hidden" name="id" value="'+ind+'" /></div>';
			});
			
			$('#active_items_list').html(activeList);
			
			if (!activeFound)
				this.activeItem = activeIdArr[0];
		},
		
		/**
		 * Renders the active item
		 * 
		 * @return bool
		 */
		renderActiveItem: function() {
			if (typeof(this.itemDetails['id'+this.activeItem]) == "undefined")
				return false;
			
			var item = this.itemDetails['id'+this.activeItem];
			
			this.clearCreationItemFields();
			
			$('#creation_item_details_name').val(item.name).removeClass('unvalidated').trigger('keyup');
			$('#creation_item_details_short_description').val(item.short_description).removeClass('unvalidated').trigger('keyup');
			$('#creation_item_details_uri').val(item.uri);
			$('#creation_item_details_github_url').val(item.github_url);
			$('#creation_item_details_layout').val(item.layout);
			$('#creation_item_details_image').val(item.image);
			$('#creation_item_details_image_small').val(item.image_small);
			this.descriptionEditor.setEditorHTML(item.description);
			this.docsEditor.setEditorHTML(item.documentation);
			$('#creation_editor_html textarea').val(item.description);
			
			$('#creation_item_details_right input').each(function() {
				var $this = $(this);
				
				if ($this.val() != '') {
					$this.parent().find('input[value="' + $this.attr('id') + '"]').parent().remove();
					$this.after(self.image_preview[0] + $this.attr('id') + self.image_preview[1] + $this.val() + self.image_preview[2]);
				}
			});
			
			$('#active_items_list input[value=id' + this.activeItem + ']').parent().addClass('active_item_tag');
			
			return true;
		},
		
		/**
		 * Handles getting a creation item on row click
		 */
		getDetails: function(id) {
			var self = this;
			
			$.post('/admin/getcreation/'+id, '', function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (data.success) {
					self.itemDetails['id'+data.creation.id] = data.creation;
					self.activeItem = data.creation.id;
					
					self.checkItemData();
					self.renderActiveItem();
				} else {
					alert(data.error);
				}
			}, 'text');
		},
		
		/**
		 * Syncs all creations with their github repos
		 */
		syncWithGitHub: function(id) {
			var self = this;
			
			$.post('/admin/synccreationswithgithub/'+id, '', function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (data.success) {
					alert('Successfully synched');
				} else {
					alert(data.error);
				}
			}, 'text');
		},
		
		/**
		 * Clears the creation item fields
		 */
		clearCreationItemFields: function() {
			$('#creation_item_details_name').val('').removeClass('unvalidated');
			$('#creation_item_details_short_description').val('').removeClass('unvalidated');
			$('#creation_item_details_uri').val('');
			$('#creation_item_details_github_url').val('');
			$('#creation_item_details_image').val('').removeClass('unvalidated');
			$('#creation_item_details_image_small').val('').removeClass('unvalidated');
			$('#creation_item_details_right .image_preview').remove();
			$('#creation_item_details_layout').val('two-column');
			this.descriptionEditor.clearEditorDoc();
			this.docsEditor.clearEditorDoc();
		},
		
		/**
		 * Stores the creation item fields
		 */
		storeCreationItemFields: function() {
			this.itemDetails['id'+this.activeItem] = {
					name: $('#creation_item_details_name').val(),
					short_description: $('#creation_item_details_short_description').val(),
					uri: $('#creation_item_details_uri').val(),
					github_url: $('#creation_item_details_github_url').val(),
					image: $('#creation_item_details_image').val(),
					image_small: $('#creation_item_details_image_small').val(),
					description: $('#creation_editor_container').is(':visible') 
								? this.descriptionEditor.getEditorHTML() 
								: $('#creation_editor_html textarea').val(),
					documentation: $('#creation_documentation_editor_container').is(':visible') 
								? this.docsEditor.getEditorHTML() 
								: $('#creation_documentation_editor_html textarea').val(),
					layout: $('#creation_item_details_layout').val()
			}
		},
		
		/**
		 * Stores the creation item fields
		 * 
		 * @return bool		false if one or more are invalid
		 */
		validateCreationItemFields: function() {
			var fields = ['creation_item_details_name', 
			              'creation_item_details_short_description'],
			    valid = true;
			
			$.each(fields, function(ind, el) {
				if ($('#'+el).val() == '') {
					$('#'+el).addClass('unvalidated');
					valid = false;
				} else
					$('#'+el).removeClass('unvalidated');
			});
			
			return valid;
		},
		
		/**
		 * Handles changes in any of the fields
		 */
		fieldChange: function() {
			this.validateCreationItemFields();
			this.storeCreationItemFields();
			this.setActiveToEdited();
		},
		
		/**
		 * Sets the active item to edited
		 */
		setActiveToEdited: function() {
			this.itemDetails['id'+this.activeItem].edited = true;
			this.checkItemData();
		},
		
		/**
		 * Saves a creation item
		 */
		saveItem: function(close) {
			var data = {},
				post = {},
				self = this;
			
			if (!this.validateCreationItemFields()) {
				alert("Please enter values for all fields");
				return false;
			}
			
			data = {
				name				: $('#creation_item_details_name').val(),
				short_description	: $('#creation_item_details_short_description').val(),
				uri					: $('#creation_item_details_uri').val(),
				github_url			: $('#creation_item_details_github_url').val(),
				image				: $('#creation_item_details_image').val(),
				image_small			: $('#creation_item_details_image_small').val(),
				description			: $('#creation_editor_container').is(':visible') 
						 				? this.descriptionEditor.getEditorHTML()
										: $('#creation_editor_html textarea').val(),
				documentation		: $('#creation_documentation_editor_container').is(':visible') 
						 				? this.docsEditor.getEditorHTML() 
										: $('#creation_documentation_editor_html textarea').val(),
				id					: this.activeItem,
				layout				: $('#creation_item_details_layout').val()
			};
			
			data.marked_up_description = this.prepareItemContent(data.description);
			data.marked_up_documentation = this.prepareItemContent(data.documentation);
			
			post = {data: $.toJSON(data)};
			
			raiseCreationMessage("Saving...", '', false);
			
			$.post('/admin/savecreation', post, function(textdata) {
				var save = $.evalJSON(textdata);
				
				if (save.success) {
					raiseCreationMessage("Saved!", 'success');
					
					if (!parseInt(data.id))
						delete self.itemDetails['id0'];
					
					if (typeof(close) != 'undefined' && close) {
						selectedIndex = $.inArray(parseInt(save.creation.id), self.selectedItemIds);
						
						if (selectedIndex != -1)
							self.selectedItemIds.splice(selectedIndex, 1);
						
						delete self.itemDetails['id'+save.creation.id];
					} else {
						self.activeItem = parseInt(save.creation.id);
						self.itemDetails['id'+save.creation.id] = save.creation;
					}
					self.checkItemData();
					self.renderActiveItem();
					self.getCreationsTableData();
				} else {
					raiseCreationMessage(save.error, 'error');
				}
			}, 'text');
		},
		
		/**
		 * Prepares the creation item content by stripping certain tags and highlighting code
		 * 
		 * @param {string}	content
		 * 
		 * @return {string}
		 */
		prepareItemContent: function(content) {
			var $content = null;
			
			content = content.replace(/(<.??font.*?>)/ig,"");
			$content = $('<div>' + content + '</div>');
			
			$('body').append($content);
			
			$content.syntaxHighlight();
			content = $content.html();
			$content.remove();
			
			return content;
		},
		
		/**
		 * Deletes a creation item
		 */
		deleteItem: function() {
			var data = new Object(),
				self = this,
				conf = confirm("Are you sure you want to permanently delete this creation?");
			
			if (!conf)
				return false;
			
			data.id = this.activeItem;
			
			if (data.id == 0) {
				raiseCreationMessage("This creation has not yet been created (ZUH?!?)", 'error');
				return false;
			}
			
			var post = new Object();
			post.data = $.toJSON(data);
			
			raiseCreationMessage("Deleting...", '', false);
			
			$.post('/admin/deletecreation', post, function(textdata) {
				var deleted = $.evalJSON(textdata);
				
				if (deleted.success) {
					raiseCreationMessage("Deleted!", 'success');
					
					selectedIndex = $.inArray(parseInt(data.id), self.selectedItemIds);
						
					if (selectedIndex != -1)
						self.selectedItemIds.splice(selectedIndex, 1);
						
					delete self.itemDetails['id'+data.id];
					
					self.checkItemData();
					self.renderActiveItem();
					self.getCreationsTableData();
				} else
					raiseCreationMessage(save.error, 'error');
			}, 'text');
		},
		
		/**
		 * Closes an item 
		 */
		closeItem: function() {
			selectedIndex = $.inArray(parseInt(this.activeItem), this.selectedItemIds);
			
			if (selectedIndex != -1) {
				this.selectedItemIds.splice(selectedIndex, 1);
			}
			
			delete self.itemDetails['id'+this.activeItem];
			self.checkItemData();
			self.renderActiveItem();
			self.getCreationsTableData();
		},
		
		/**
		 * Gets and repopulates the data in the creation item table
		 */
		getCreationsTableData: function() {
			var self = this;
			
			$.post('/admin/getcreations', '', function(textdata) {
				var items = $.evalJSON(textdata),
					creationsLength = 0,
					i;
				
				if (items.success)
					creationsData = items.creations;
				else
					creationsData = [];
				
				$('#creations_list').jqGrid('clearGridData');
				creationsLength = creationsData.length;
				
				for(i = 0; i<=creationsLength; i++)
					$("#creations_list").jqGrid('addRowData',i+1,creationsData[i]);
				
				self.setSelectedRows();
			}, 'text');
		},
		
		toggleEditorHtml: function() {
			if ($('#html_creation_item').text() == 'View HTML') {
				var content = this.descriptionEditor.getEditorHTML();
				
				$('#creation_editor_html').show();
				$('#creation_editor_container').hide();
				$('#creation_editor_html textarea').val(content);
				
				$('#html_creation_item').text('View Editor');
			} else {
				var content = $('#creation_editor_html textarea').val();;
				
				$('#creation_editor_html').hide();
				$('#creation_editor_container').show();
				this.descriptionEditor.setEditorHTML(content);
				
				$('#html_creation_item').text('View HTML');
			}
		},
		
		toggleDocsEditorHtml: function() {
			if ($('#html_creation_documentation_item').text() == 'View HTML') {
				var content = this.docsEditor.getEditorHTML();
				
				$('#creation_documentation_editor_html').show();
				$('#creation_documentation_editor_container').hide();
				$('#creation_documentation_editor_html textarea').val(content);
				
				$('#html_creation_documentation_item').text('View Editor');
			} else {
				var content = $('#creation_documentation_editor_html textarea').val();;
				
				$('#creation_documentation_editor_html').hide();
				$('#creation_documentation_editor_container').show();
				this.docsEditor.setEditorHTML(content);
				
				$('#html_creation_documentation_item').text('View HTML');
			}
		}
	};
	
	/**
	 * Raises a message (either neutral, error, or success type) and either fades it away after a few seconds or it doesn't
	 * 
	 * @param string	message	<= string to display
	 * @param string	type <= class to set the message to. 'error', 'success', or null/''
	 * @param bool		fade <= true/null to have it fade, false to let it stay indefinitely
	 */
	function raiseCreationMessage(message, type, fade) {
		if (typeof(type) == 'undefined')
			type = '';
		
		$('#creation_message').text(message).attr('class', type).show();
		
		if (typeof(fade) != 'undefined' && !fade) {
			setTimeout(function() {
				$('#creation_message').hide();
			}, 3000);
		}
	}
	
	window.creations = new creations();
})();


/*///////////////////////////////////////////////////////////////
 * PORTFOLIO SECTION
 *///////////////////////////////////////////////////////////////

/******************
 * portfolio object
 */
(function() {
	function portfolio(){};
	
	portfolio.prototype = {
		//object of selected item details (and new item)...included is 'edited' key..set to true if user enters an input box
		itemDetails: {},
		//active item in the item details area by id (0 if new...new is exclusive details box...can't do multiple)
		activeItem: 0,
		//data template for new items
		blankItem: {
			id: 0,
			name: '',
			short_description: '',
			description: '',
			marked_up_description: '',
			uri: '',
			live_url: '',
			image: '',
			image_small: '',
			time: ''
		},
		//array of selected item ids; for solving the sort deselect bug in jqgrid
		selectedItemIds: [],
		
		//image preview scaffold for the image inputs
		image_preview: ['<div class="image_preview"><input type="hidden" value="', 
						'" /><img src="',
						'" /><div class="image_preview_x">X</div></div>'],
		
		/* the description editor
		 * yahoo editor
		 */
		descriptionEditor: null,
		
		/**
		 * Initiates the portfolio manager page
		 */
		initiate: function() {
			var self = this;
			
			this.initiateAdminGrid();
			this.initiateTextEditor();
			characters.addField($('#portfolio_item_details_short_description'), 400);
			$('#portfolio_item_details_time').datepicker({ dateFormat: 'yy-mm-dd' });
			
			//open new portfolio item form
			$('#add_portfolio_top').click(function() {
				self.toggleAddItem();
			});
			
			//save item
			$('#save_portfolio').click(function() {
				self.saveItem();
			});
			//save and close item
			$('#save_and_close_portfolio').click(function() {
				self.saveItem(true);
			});
			//close item
			$('#close_portfolio').click(function() {
				self.closeItem();
			});
			//delete item
			$('#delete_portfolio').click(function() {
				self.deleteItem();
			});
			//toggle item description html
			$('#html_portfolio_item').click(function() {
				self.toggleEditorHtml();
			});
			
			//sets up item fields to be validated on change
			var selector = '#portfolio_item_details_name, #portfolio_item_details_short_description, #portfolio_item_details_time,' +
							'#portfolio_editor_html textarea';
			$(selector).change(function() {
				self.fieldChange();
			});
			
			//active items list click event
			$('#active_items_list > div').live('click', function() {
				self.activeItem = parseInt($('input[name=id]', $(this)).val().substr(2));
				self.checkItemData();
				self.renderActiveItem();
			});
			
			//set up the syntax highlighter
			$.SyntaxHighlighter.init({
				lineNumbers : false,
				prettifyBaseUrl : ''
			});
			
			//set up the filePicker plugin for the image fields
			$('#portfolio_item_details_image, #portfolio_item_details_image_small').filePicker({
				dataSource		: '/admin/getdirectory',
				baseDirectory	: '/images',
				afterSelectFile : function(data) {
					this.val(data.path);
					
					this.parent().find('input[value="' + this.attr('id') + '"]').parent().remove();
					this.after(self.image_preview[0] + this.attr('id') + self.image_preview[1] + data.path + self.image_preview[2]);
					
					self.fieldChange();
				}
			});
			
			$('#portfolio_item_area').delegate('.image_preview_x', 'click', function() {
				var $this = $(this),
					id = $this.siblings('input').val(),
					conf = confirm("Are you sure you want to remove this image?");
				
				if (!conf) return false;
				
				$this.parent().remove();
				$('#'+id).val('');
			});
		},
		
		/**
		 * Initiates the portfolio admin datagrid
		 */
		initiateAdminGrid: function() {
			var self = this,
				i = 0,
				portfolioLength = portfolioData.length;
			
			$('#portfolio_list').jqGrid({
				datatype: "local",
				height: 200,
				colNames: ['Time', 'Name', 'Short Description', 'ID'],
				colModel: [{name:'time', index:'time', width:120, sorttype:'text', align:'center'},
				           {name:'name', index:'name', width:180, sorttype:'text'},
				           {name:'short_description', index:'short_description', width:460, sorttype:'text'},
				           {name:'id', index:'id', width:40, sorttype:'int', align:'center'}],
				multiselect: true,
				caption: "Portfolio",
				//events
				onSelectRow: function(id, status) {
					self.selectRow(id, status, true);
				},
				onSelectAll: function(aIds, status) {
					//loop through rows, then render afterward
					$.each(aIds, function(ind, el) {
						self.selectRow(el, status, false)
					});
					self.checkItemData();
					self.renderActiveItem();
				},
				gridComplete: function() {
					self.setSelectedRows();
				}
			});
			
			for(i; i<=portfolioLength; i++)
				$("#portfolio_list").jqGrid('addRowData',i+1,portfolioData[i]);
		},
		
		/**
		 * Handles a row selection on the portfolio table
		 * 
		 * @param int	rowId
		 * @param bool	status
		 * @param bool	render = whether or not to render the data after set (false for select all)
		 */
		selectRow: function(rowId, status, render) {
			var itemId = parseInt($('#portfolio_list').jqGrid('getRowData', rowId)['id']),
				selectedIndex = $.inArray(itemId, this.selectedItemIds);
			
			//fixes how it returns header row in select all
			if (typeof(rowId) == "undefined")
				return false;
			
			if (status) {
				if (selectedIndex == -1) {
					this.selectedItemIds.push(itemId);
					this.getDetails(itemId);
				} else {
					this.activeItem = itemId;
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			} else {
				if (selectedIndex != -1) {
					this.selectedItemIds.splice(selectedIndex, 1);
					delete this.itemDetails['id'+itemId];
					
					if (render) {
						this.checkItemData();
						this.renderActiveItem();
					}
				}
			}
		},
		
		/**
		 * Reselects all selected rows in the jqgrid after sorting or reloading the table data
		 */
		setSelectedRows: function() {
			$.each(this.selectedItemIds, function(ind, el) {
				var rowId = $('td[aria-describedby=portfolio_list_id][title='+el+']').parent().attr('id');
				$("#portfolio_list").jqGrid('setSelection', rowId, false);
			});
		},
		
		/**
		 * Initiates the wysiwyg editor on the portfolio admin page
		 */
		initiateTextEditor: function() {
			this.descriptionEditor = new YAHOO.widget.Editor('portfolio_editor', {
			    height: '150px',
			    width: '100%',
			    dompath: true, //Turns on the bar at the bottom
			    animate: true //Animates the opening, closing and moving of Editor windows
			});
			this.descriptionEditor.render();
		},
		
		/**
		 * gets the length of the itemDetails object
		 */
		getItemDetailsLength: function() {
			var count = 0;
			
			$.each(this.itemDetails, function(ind, el) {
				count++;
			});
			
			return count;
		},
		
		/**
		 * Toggles the add item
		 */
		toggleAddItem: function() {
			//TODO: add check to see if unsaved changes
			var item_area = $('#portfolio_item_area');
		
			if (typeof(this.itemDetails['id0']) == "undefined") {
				if (this.getItemDetailsLength() > 0)
					this.storeItemFields();
				
				this.activeItem = 0;
				this.itemDetails['id0'] = this.blankItem;
				this.checkItemData();
				this.renderActiveItem();
			} else {
				delete this.itemDetails['id0'];
				this.checkItemData();
				this.renderActiveItem();
			}
		},
		
		/**
		 * Sets presentation state based on the items in the itemDetails array 
		 */
		checkItemData: function () {
			var activeList = '',
				activeFound = false
				activeIdArr = [],
				self = this;
			
			//if there aren't any items at all
			if (this.getItemDetailsLength() == 0) {
				this.clearItemFields();
				$('#add_portfolio_top').text("Add New Item");
				$('#portfolio_item_area').hide();
				$('#active_items_list').html('');
				return false;
			}
			
			$('#portfolio_item_area').show();
			
			//if there isn't a "new" item slot
			if (typeof(this.itemDetails['id0']) == "undefined")
				$('#add_portfolio_top').text("Add New Item");
			else
				$('#add_portfolio_top').text("Cancel Add");
			
			$.each(this.itemDetails, function(ind, el) {
				var activeClass = '',
					name = $.trim(el.name) == '' ? 'New Item' : el.name;
				
				if (ind.substr(2) == self.activeItem) {
					activeFound = true;
					activeClass += 'active_item_tag ';
				}
				
				if (el.edited) 
					activeClass += 'editing_item_tag';
				
				if (activeClass.length) 
					activeClass = ' class="' + activeClass + '"';
				
				activeIdArr.push(ind.substr(2));
				activeList += '<div'+activeClass+'>'+name+'<input type="hidden" name="id" value="'+ind+'" /></div>';
			});
			
			$('#active_items_list').html(activeList);
			
			if (!activeFound)
				this.activeItem = activeIdArr[0];
		},
		
		/**
		 * Renders the active item
		 * 
		 * @return bool
		 */
		renderActiveItem: function() {
			if (typeof(this.itemDetails['id'+this.activeItem]) == "undefined")
				return false;
			
			var item = this.itemDetails['id'+this.activeItem];
			
			this.clearItemFields();
			
			$('#portfolio_item_details_name').val(item.name).removeClass('unvalidated').trigger('keyup');
			$('#portfolio_item_details_short_description').val(item.short_description).removeClass('unvalidated').trigger('keyup');
			$('#portfolio_item_details_uri').val(item.uri);
			$('#portfolio_item_details_live_url').val(item.live_url);
			$('#portfolio_item_details_time').val(item.time).removeClass('unvalidated');
			$('#portfolio_item_details_image').val(item.image);
			$('#portfolio_item_details_image_small').val(item.image_small);
			this.descriptionEditor.setEditorHTML(item.description);
			$('#portfolio_editor_html textarea').val(item.description);
			
			$('#portfolio_item_details_right input').each(function() {
				var $this = $(this);
				
				if ($this.val() != '') {
					$this.parent().find('input[value="' + $this.attr('id') + '"]').parent().remove();
					$this.after(self.image_preview[0] + $this.attr('id') + self.image_preview[1] + $this.val() + self.image_preview[2]);
				}
			});
			
			$('#active_items_list input[value=id' + this.activeItem + ']').parent().addClass('active_item_tag');
			
			return true;
		},
		
		/**
		 * Handles getting a portfolio item on row click
		 */
		getDetails: function(id) {
			var self = this;
			
			$.post('/admin/getportfolioitem/'+id, '', function(textdata) {
				var data = $.evalJSON(textdata);
				
				if (data.success) {
					self.itemDetails['id'+data.item.id] = data.item;
					self.activeItem = data.item.id;
					
					self.checkItemData();
					self.renderActiveItem();
				} else {
					alert(data.error);
				}
			}, 'text');
		},
		
		/**
		 * Clears the portfolio item fields
		 */
		clearItemFields: function() {
			$('#portfolio_item_details_name').val('').removeClass('unvalidated');
			$('#portfolio_item_details_short_description').val('').removeClass('unvalidated');
			$('#portfolio_item_details_uri').val('');
			$('#portfolio_item_details_live_url').val('');
			$('#portfolio_item_details_time').val('').removeClass('unvalidated');
			$('#portfolio_item_details_image').val('').removeClass('unvalidated');
			$('#portfolio_item_details_image_small').val('').removeClass('unvalidated');
			$('#portfolio_item_details_right .image_preview').remove();
			this.descriptionEditor.clearEditorDoc();
		},
		
		/**
		 * Stores the portfolio item fields
		 */
		storeItemFields: function() {
			this.itemDetails['id'+this.activeItem] = {
					name: $('#portfolio_item_details_name').val(),
					short_description: $('#portfolio_item_details_short_description').val(),
					uri: $('#portfolio_item_details_uri').val(),
					live_url: $('#portfolio_item_details_live_url').val(),
					time: $('#portfolio_item_details_time').val(),
					image: $('#portfolio_item_details_image').val(),
					image_small: $('#portfolio_item_details_image_small').val(),
					description: $('#portfolio_editor_container').is(':visible') 
								? this.descriptionEditor.getEditorHTML() 
								: $('#portfolio_editor_html textarea').val()
			}
		},
		
		/**
		 * Stores the portfolio item fields
		 * 
		 * @return bool		false if one or more are invalid
		 */
		validateItemFields: function() {
			var fields = ['portfolio_item_details_name', 
			              'portfolio_item_details_short_description',
						  'portfolio_item_details_time'],
			    valid = true;
			
			$.each(fields, function(ind, el) {
				if ($('#'+el).val() == '') {
					$('#'+el).addClass('unvalidated');
					valid = false;
				} else
					$('#'+el).removeClass('unvalidated');
			});
			
			return valid;
		},
		
		/**
		 * Handles changes in any of the fields
		 */
		fieldChange: function() {
			this.validateItemFields();
			this.storeItemFields();
			this.setActiveToEdited();
		},
		
		/**
		 * Sets the active item to edited
		 */
		setActiveToEdited: function() {
			this.itemDetails['id'+this.activeItem].edited = true;
			this.checkItemData();
		},
		
		/**
		 * Saves a portfolio item
		 */
		saveItem: function(close) {
			var data = {},
				post = {},
				self = this;
			
			if (!this.validateItemFields()) {
				alert("Please enter values for all fields");
				return false;
			}
			
			data = {
				name				: $('#portfolio_item_details_name').val(),
				short_description	: $('#portfolio_item_details_short_description').val(),
				uri					: $('#portfolio_item_details_uri').val(),
				live_url			: $('#portfolio_item_details_live_url').val(),
				time				: $('#portfolio_item_details_time').val(),
				image				: $('#portfolio_item_details_image').val(),
				image_small			: $('#portfolio_item_details_image_small').val(),
				description			: $('#portfolio_editor_container').is(':visible') 
						 				? this.descriptionEditor.getEditorHTML()
										: $('#portfolio_editor_html textarea').val(),
				id					: this.activeItem
			};
			
			data.marked_up_description = this.prepareItemContent(data.description);
			
			post = {data: $.toJSON(data)};
			
			raiseMessage("Saving...", '', false);
			
			$.post('/admin/saveportfolioitem', post, function(textdata) {
				var save = $.evalJSON(textdata);
				
				if (save.success) {
					raiseMessage("Saved!", 'success');
					
					if (!parseInt(data.id))
						delete self.itemDetails['id0'];
					
					if (typeof(close) != 'undefined' && close) {
						selectedIndex = $.inArray(parseInt(save.creation.id), self.selectedItemIds);
						
						if (selectedIndex != -1)
							self.selectedItemIds.splice(selectedIndex, 1);
						
						delete self.itemDetails['id'+save.item.id];
					} else {
						self.activeItem = parseInt(save.item.id);
						self.itemDetails['id'+save.item.id] = save.item;
					}
					self.checkItemData();
					self.renderActiveItem();
					self.getTableData();
				} else {
					raiseMessage(save.error, 'error');
				}
			}, 'text');
		},
		
		/**
		 * Prepares the portfolio item content by stripping certain tags and highlighting code
		 * 
		 * @param {string}	content
		 * 
		 * @return {string}
		 */
		prepareItemContent: function(content) {
			var $content = null;
			
			content = content.replace(/(<.??font.*?>)/ig,"");
			$content = $('<div>' + content + '</div>');
			
			$('body').append($content);
			
			$content.syntaxHighlight();
			content = $content.html();
			$content.remove();
			
			return content;
		},
		
		/**
		 * Deletes a creation item
		 */
		deleteItem: function() {
			var data = new Object(),
				self = this,
				conf = confirm("Are you sure you want to permanently delete this item?");
			
			if (!conf)
				return false;
			
			data.id = this.activeItem;
			
			if (data.id == 0) {
				raiseMessage("This portfolio item has not yet been created (ZUH?!?)", 'error');
				return false;
			}
			
			var post = new Object();
			post.data = $.toJSON(data);
			
			raiseMessage("Deleting...", '', false);
			
			$.post('/admin/deleteportfolioitem', post, function(textdata) {
				var deleted = $.evalJSON(textdata);
				
				if (deleted.success) {
					raiseMessage("Deleted!", 'success');
					
					selectedIndex = $.inArray(parseInt(data.id), self.selectedItemIds);
						
					if (selectedIndex != -1)
						self.selectedItemIds.splice(selectedIndex, 1);
						
					delete self.itemDetails['id'+data.id];
					
					self.checkItemData();
					self.renderActiveItem();
					self.getTableData();
				} else
					raiseMessage(save.error, 'error');
			}, 'text');
		},
		
		/**
		 * Closes an item 
		 */
		closeItem: function() {
			selectedIndex = $.inArray(parseInt(this.activeItem), this.selectedItemIds);
			
			if (selectedIndex != -1) {
				this.selectedItemIds.splice(selectedIndex, 1);
			}
			
			delete self.itemDetails['id'+this.activeItem];
			self.checkItemData();
			self.renderActiveItem();
			self.getTableData();
		},
		
		/**
		 * Gets and repopulates the data in the portfolio item table
		 */
		getTableData: function() {
			var self = this;
			
			$.post('/admin/getportfolio', '', function(textdata) {
				var items = $.evalJSON(textdata),
					portfolioLength = 0,
					i;
				
				if (items.success)
					portfolioData = items.items;
				else
					portfolioData = [];
				
				$('#portfolio_list').jqGrid('clearGridData');
				portfolioLength = portfolioData.length;
				
				for(i = 0; i<=portfolioLength; i++)
					$("#portfolio_list").jqGrid('addRowData',i+1,portfolioData[i]);
				
				self.setSelectedRows();
			}, 'text');
		},
		
		toggleEditorHtml: function() {
			if ($('#html_portfolio_item').text() == 'View HTML') {
				var content = this.descriptionEditor.getEditorHTML();
				
				$('#portfolio_editor_html').show();
				$('#portfolio_editor_container').hide();
				$('#portfolio_editor_html textarea').val(content);
				
				$('#html_portfolio_item').text('View Editor');
			} else {
				var content = $('#portfolio_editor_html textarea').val();;
				
				$('#portfolio_editor_html').hide();
				$('#portfolio_editor_container').show();
				this.descriptionEditor.setEditorHTML(content);
				
				$('#html_portfolio_item').text('View HTML');
			}
		},
	};
	
	/**
	 * Raises a message (either neutral, error, or success type) and either fades it away after a few seconds or it doesn't
	 * 
	 * @param string	message	<= string to display
	 * @param string	type <= class to set the message to. 'error', 'success', or null/''
	 * @param bool		fade <= true/null to have it fade, false to let it stay indefinitely
	 */
	function raiseMessage(message, type, fade) {
		if (typeof(type) == 'undefined')
			type = '';
		
		$('#portfolio_message').text(message).attr('class', type).show();
		
		if (typeof(fade) != 'undefined' && !fade) {
			setTimeout(function() {
				$('#portfolio_message').hide();
			}, 3000);
		}
	}
	
	window.portfolio = new portfolio();
})();