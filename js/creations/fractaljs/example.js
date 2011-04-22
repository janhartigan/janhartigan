$(function() {
	$('#fractal_picker > div').click(function() {
		var maxIt = 500;
		
		//fade the fractal picker out, then set the css properties so it displays as a floated left list above the canvas
		$('#fractal_picker').fadeOut(300);
		
		$('#fractal_picker > div').css({
			position	: 'relative',
			width		: 'auto',
			height		: 'auto',
			'float'		: 'left',
			bottom		: 'auto',
			left		: 'auto',
			marginRight	: '10px'
		});
		
		$('#fractal_picker').css('height', 'auto');
		
		$('#fractal_picker').fadeIn(300);
		
		switch(this.id) {
			case 'fractal_picker_large':
				$('#fractal_canvas').attr('width', '590').attr('height', '380'); maxIt = 300; break;
			case 'fractal_picker_medium':
				$('#fractal_canvas').attr('width', '280').attr('height', '280'); maxIt = 400; break;
			case 'fractal_picker_small':
				$('#fractal_canvas').attr('width', '150').attr('height', '150'); break;
		}
		
		$('#fractal_canvas').fractaljs({maxIterations: maxIt});
		$('#fractal_canvas').fadeIn(300);
	});
});