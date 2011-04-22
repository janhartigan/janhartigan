<?php if ($news_items['success']) {?>
<ul id="news_list">
	<?php foreach ($news_items['items'] as $item) {?>
		<li>
		<?php echo $this->load->view('news_item_small', array('item'=>$item), true)?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no blog items to display</em></div>
<?php }?>