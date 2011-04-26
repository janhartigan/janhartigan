<div id="portfolio_list_main">
	<?php if ($portfolio['success']) {?>
		<?php $len = sizeof($portfolio['items']);?>
		<?php foreach ($portfolio['items'] as $i=>$item) {?>
			<?php echo $this->load->view('portfolio/portfolio_home_item', array('item'=>$item), true);?>
			<?php if ($i !== $len-1) {?>
				<hr/>
			<?php }?>
		<?php }?>
	<?php } else {?>
		<em>There are no portfolio items to display</em>
	<?php }?>
</div>