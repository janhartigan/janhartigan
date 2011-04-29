<?php $c = $creation['creation']?>

<?php if ($c['layout']=='two-column') {?>
	<div class="creation_info_two_column">
		<?php if ($c['github_url']) {?>
			<a href="<?php echo $c['github_url']?>" class="github_link" target="_blank">
				<img src="/images/creations/github.png" />
			</a>
		<?php }?>
		
		<h2>Creation - <?php echo $c['name']?></h2>
		<div><em>
			<time datetime="<?php echo $c['time']?>"><?php echo date('F j, Y', strtotime($c['time']))?></time>
		</em></div>
		
		<?php if ($c['image']) {?>
			<img src="<?php echo $c['image']?>" class="main_creation_image" />
		<?php }?>
		
		<?php if (isset($creation_tools) && $creation_tools['success']) {?>
			<h3>Tools</h3>
			<div id="creation_tools">
				<?php $i=1; foreach ($creation_tools['tools'] as $tool) { echo $tool['name'].($i==sizeof($creation_tools['tools'])?' ':', '); $i++;}?>
			</div>
		<?php }?>
	</div>
<?php } else if ($c['layout']=='full-width') {?>
	<div class="creation_info_full_width">
	
	</div>
<?php }?>