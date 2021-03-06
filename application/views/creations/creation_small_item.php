<a href="/creations/<?php echo $creation['uri'] ?>" class="creation_small_link">
	<?php if ($creation['image_small']) {?>
		<img src="<?php echo $creation['image_small']?>" class="creation_small_image" />
	<?php }?>
	<h3><?php echo $creation['name']?></h3>
	<div class="creation_small_time">
		<?php $time = $creation['update_time'] ? $creation['update_time'] : $creation['time']?>
		<time datetime="<?php echo $creation['time']?>" pubdate><?php echo date('F j, Y', strtotime($time))?></time>
	</div>
	
	<div class="creation_short_description"><?php echo $creation['short_description']?></div>
	
	<div class="clear"></div>
</a>