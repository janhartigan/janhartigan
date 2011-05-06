$(document).ready(function() {
	
	
	//login focus
	$('#login_username').focus();
});


/**
 * The articles object handles all things in the articles section of the site
 */
(function() {
	/**
	 * Constructor function for the articles object
	 */
	function articles() {
		//call the init
		this.init();
	};
	
	//create the articles object via prototype extension
	articles.prototype = {
		/* The current page
		 * int
		 */
		page: 1,
		
		/* The current list style
		 * string 
		 */
		list_style: 'blurb',
		
		
		/**
		 * The init function
		 */
		init: function() {
			var self = this;
			
			//set up the list style selector on the articles landing page
			$article_list_selector_div = $('#article_list_selector > div');
			
			$article_list_selector_div.click(function() {
				self.list_style = $(this).text().toLowerCase();
				$article_list_selector_div.removeClass('selected');
				$(this).addClass('selected');
				self.getArticles();
			});
		},
		
		/**
		 * Requests the articles list based on the current set of properties
		 */
		getArticles: function() {
			var self = this,
				data = {
					page: this.page,
					list_style: this.list_style
				};
			
			//put something in here that shows transition
			
			$.ajax({
				type	: 'POST',
				url		: '/articles/getArticles',
				data	: {'data':$.toJSON(data)},
				dataType: 'json',
				success	: function(ret) {
					if (ret.success) {
						$('#article_list').replaceWith(ret.content);
					} else
						alert(ret.error);
				}
			});
		}
	};
	
	window.articles = new articles();
})();
