<?php if ($articles['success']) {?>
<ul id="news_list">
	<?php foreach ($articles['items'] as $item) {?>
		<li>
		<?php echo $this->load->view('articles/article_small', array('item'=>$item), true)?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no articles to display</em></div>
<?php }?>