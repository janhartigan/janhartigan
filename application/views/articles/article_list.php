<?php if ($articles['success']) {?>
<ul id="article_list">
	<?php $len = sizeof($articles['items']);?>
	<?php foreach ($articles['items'] as $i=>$item) {?>
		<li<?php echo $i==($len-1) ? ' style="border-bottom:none"' : ''?>>
			<?php if ($list_style == 'blurb') {?>
				<?php echo $this->load->view('articles/article_small', array('item'=>$item), true)?>
			<?php } else {?>
				<?php echo $this->load->view('articles/article_headline', array('item'=>$item), true)?>
			<?php }?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no articles to display</em></div>
<?php }?>