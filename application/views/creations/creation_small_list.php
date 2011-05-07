<h2 class="underline"><a href="/creations">Creations</a></h2>
<?php if ($creations['success']) {?>
<ul id="creations_small_list">
	<?php $len = sizeof($creations['creations']);?>
	<?php foreach ($creations['creations'] as $i=>$item) {?>
		<li<?php echo $i==($len-1) ? ' style="border-bottom:none"' : ''?>>
		<?php echo $this->load->view('creations/creation_small_item', array('creation'=>$item), true)?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no creations to display</em></div>
<?php }?>