<div id="creations_list_main">
	<?php if ($creations['success']) {?>
		<?php $i = 0;?>
		<?php foreach ($creations['creations'] as $item) {?>
			<?php echo $this->load->view('creations/creations_landing_item', array('creation'=>$item, 'creation_num'=>$i), true);
					$i++;?>
		<?php }?>
	<?php } else {?>
		<em>There are no creations to display</em>
	<?php }?>
</div>