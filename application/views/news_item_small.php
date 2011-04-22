<article>
	<header>
		<a href="<?php echo base_url().'articles/'.$item['title_url']?>" class="news_item_header_link">
			<h2><?php echo $item['title']?></h2>
		</a>
		<div class="item_time"><time datetime="<?php echo $item['date']?>" pubdate><?php echo date('F j, Y', strtotime($item['date']))?></time></div>
	</header>
	<?php if ($item['image']) {?>
		<img src="<?php echo $item['image']?>" class="item_main_image" />
	<?php }?>
	<div class="item_description"><?php echo $item['marked_up_content_short']?></div>
	
	<a href="<?php echo base_url().'articles/'.$item['title_url']?>" class="news_item_read_more_link">Read the whole article...</a>
	<div class="clear"></div>
</article>