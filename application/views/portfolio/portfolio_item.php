<div id="portfolio_item">
	<div class="top_section">
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
					<div><em>(Note: it's possible that this site no longer accurately represents the work I did for this client)</em></div>
				<?php } else {?>
					<em>This site is no longer online.</em>
				<?php }?>
			</div>
		</div>
		
		<div class="clear"></div>
	</div>
	
	<div id="portfolio_disqus_container">
		<h3>Comments</h3>
		<div id="disqus_thread"></div>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments.</a></noscript>
	</div>
	
	<h3>Description</h3>
	<div class="portfolio_description">
		<?php echo $item['marked_up_description']?>
		
		<p>If you want to ask me more questions about this project, or if you're interested in my services, please check out my 
		<a href="/contact">contact page</a>.</p>
	</div>
</div>