<a href="/portfolio/<?php echo $item['uri']?> " class="portfolio_home_item">
	<img src="<?php echo $item['image']?>" width="600" height="220" />
	
	<div class="portfolio_left_info">
		<h3><?php echo $item['name']?></h3>
		<div class="portfolio_small_time">
			<time datetime="<?php echo $item['time']?>" pubdate><?php echo date('F Y', strtotime($item['time']))?></time>
		</div>
		<div class="portfolio_short_description">
			<?php echo $item['short_description']?>
		</div>
		<div class="portfolio_tools">
			<strong>Tools:</strong>
			<?php echo $item['tools']?>
		</div>
	</div>
	
	<div class="clear"></div>
</a>