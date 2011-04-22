$(function() {
	$('#filepicker').filePicker({
		horizontalAlign	: 'left',
		dataSource		: '/creations/filepicker/getdirectory',
		baseDirectory	: '/images/creations',
		afterSelectFile : function(data) {
			var image_preview = ['<div class="image_preview"><input type="hidden" value="', 
								'" /><img src="',
								'" /></div>'];
			
			this.val(data.path);
			
			this.parent().find('input[value="' + this.attr('id') + '"]').parent().remove();
			this.after(image_preview[0] + this.attr('id') + image_preview[1] + data.path + image_preview[2]);
		}
	});
});
