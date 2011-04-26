<div id="portfolio_item">
	<img src="<?php echo $item['image']?>" width="600" height="220" />
		
	<div class="portfolio_left_info">
		<h2><?php echo $item['name']?></h2>
		<div class="portfolio_small_time">
			<time datetime="<?php echo $item['time']?>" pubdate><?php echo date('F Y', strtotime($item['time']))?></time>
		</div>
		<div class="portfolio_tools">
			<strong>Tools:</strong>
			<?php echo $item['tools']?>
		</div>
		<div class="portfolio_external_link">
			<strong>Link:</strong>
			<?php if ($item['live_url']) {?>
				<a href="<?php echo $item['live_url']?>"><?php echo $item['live_url']?></a>
			<?php } else {?>
				<em>This site is no longer online.</em>
			<?php }?>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<hr/>
	
	<div class="portfilio_description">
		<?php echo $item['marked_up_description']?>
	</div>
</div>