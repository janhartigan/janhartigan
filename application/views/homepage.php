<div id="left_main">
	<?php $this->load->view('articles/article_list', array('page' => 1, 'list_style' => 'blurb'));?>
</div>
<div id="right_main">
	<?php $this->load->view('creations/creation_small_list');?>
	<?php $this->load->view('portfolio/portfolio_small_list');?>
</div>