<a href="/creations/<?php echo $creation['uri']?>" class="creations_landing_item"<?php echo $creation_num % 3 === 0 ? ' style="clear:left"' : ''?>>
	<h3><?php echo $creation['name']?></h3>
	<?php if ($creation['image_small']) {?>
		<img src="<?php echo $creation['image_small']?>" />
	<?php }?>
	
	<div class="creation_right_info">
		<div class="creation_small_time">
			<strong>Published:</strong>
			<time datetime="<?php echo $creation['time']?>" pubdate><?php echo date('Y-m-d', strtotime($creation['time']))?></time>
		</div>
		<div class="creation_small_time">
			<strong>Updated:</strong>
			<time datetime="<?php echo $creation['update_time']?>"><?php echo date('Y-m-d', strtotime($creation['update_time']))?></time>
		</div>
		<hr/>
		<div class="creation_tools">
			<strong>Tools:</strong>
			<?php echo $creation['tools']?>
		</div>
	</div>
	
	
	<div class="creation_short_description"><?php echo $creation['short_description']?></div>
	
	<div class="clear"></div>
</a>