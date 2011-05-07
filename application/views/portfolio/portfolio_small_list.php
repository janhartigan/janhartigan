<h2 class="underline"><a href="/portfolio">Portfolio</a></h2>
<?php if ($portfolio['success']) {?>
<ul id="portfolio_small_list">
	<?php $len = sizeof($portfolio['items']);?>
	<?php foreach ($portfolio['items'] as $i=>$item) {?>
		<li<?php echo $i==($len-1) ? ' style="border-bottom:none"' : ''?>>
		<?php echo $this->load->view('portfolio/portfolio_small_item', array('item'=>$item), true)?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no portfolio items to display</em></div>
<?php }?>