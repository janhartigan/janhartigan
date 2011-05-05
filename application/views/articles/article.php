<div id="news_disqus_container">
	<h3>Comments</h3>
	<div id="disqus_thread"></div>
	<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments.</a></noscript>
</div>

<article id="main_news_article">
	<header>
		<h2><?php echo $item['title']?></h2>
		<div class="item_time">
			<time datetime="<?php echo $item['date']?>" pubdate><?php echo date('F j, Y', strtotime($item['date']))?></time>
		</div>
	</header>
	<?php if ($item['image']) {?>
		<img src="<?php echo $item['image']?>" class="item_main_image" />
	<?php }?>
	<div class="item_description"><?php echo $item['marked_up_content']?></div>
	
	<div class="clear"></div>
</article>