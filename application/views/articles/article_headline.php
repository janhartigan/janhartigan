<h2>
	<a href="<?php echo base_url().'articles/'.$item['title_url']?>" class="news_item_header_link">
		<?php echo $item['title']?>
	</a>
</h2>
<div class="item_time"><time datetime="<?php echo $item['date']?>" pubdate><?php echo date('F j, Y', strtotime($item['date']))?></time></div>