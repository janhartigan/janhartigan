<?php if ($creations['success']) {?>
<ul id="creations_list">
	<?php foreach ($creations['creations'] as $item) {?>
		<li>
		<?php echo $this->load->view('creation_small', array('creation'=>$item), true)?>
		</li>
	<?php }?>
</ul>
<?php } else {?>
<div><em>There are no creations to display</em></div>
<?php }?>