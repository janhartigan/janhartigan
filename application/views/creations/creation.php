<?php $c = $creation['success'] ? $creation['creation'] : $creation['error']?>
<div id="creation">
<?php if ($creation['success']) {?>
	<?php $c = $creation['creation'];?>
	
	<div id="creation_top_section">
	<?php if ($c['layout'] == 'two-column') {
			echo $this->load->view('creations/creation_info', null, true);?>
			<div id="creation_example">
				<h3>Example</h3>
				<?php echo $this->load->view($creation_view.'creation.php', null, true);?>
			</div>
	<?php } else {?>
			<div id="creation_example">
				<h3>Example</h3>
				<?php 
				echo $this->load->view($creation_view.'creation.php', null, true);	
				echo $this->load->view('creations/creation_info', null, true);?>
			</div>
	<?php }?>
		
		<div class="clear"></div>
	</div>
	
	<div id="creation_disqus_container">
		<h3>Comments</h3>
		<div id="disqus_thread"></div>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments.</a></noscript>
	</div>
	
	<h3>Documentation</h3>
	<div id="creation_documentation">
		<?php echo $c['marked_up_documentation']?>
	</div>
<?php } else {?>
	<h2>Creation - Not Found</h2>
	
	<div><em>This creation could not be found</em></div>
<?php }?>
</div>