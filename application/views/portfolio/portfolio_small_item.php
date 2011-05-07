<a href="/portfolio/<?php echo $item['uri'] ?>" class="portfolio_small_link">
	<?php if ($item['image_small']) {?>
		<img src="<?php echo $item['image_small']?>" class="portfolio_small_image" />
	<?php }?>
	<h3><?php echo $item['name']?></h3>
	<div class="portfolio_small_time">
		<time datetime="<?php echo $item['time']?>" pubdate><?php echo date('F j, Y', strtotime($item['time']))?></time>
	</div>
	
	<div class="portfolio_short_description"><?php echo $item['short_description']?></div>
	
	<div class="clear"></div>
</a>