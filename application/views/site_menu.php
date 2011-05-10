<div id="site_nav_menu">
	<menu>
		<li>
			<a href="<?php echo base_url()?>" class="home_icon <?php echo $selected_menu == 'home' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/home_icon.png" />
				Home
			</a>
		</li>
		<li>
			<a href="<?php echo base_url()?>articles" class="blog_icon <?php echo $selected_menu == 'articles' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/blog_icon.png" />
				Articles
			</a>
		</li>
		<li>
			<a href="<?php echo base_url()?>portfolio" class="portfolio_icon <?php echo $selected_menu == 'portfolio' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/portfolio_icon.png" />
				Portfolio
			</a>
		</li>
		<li>
			<a href="<?php echo base_url()?>creations" class="creations_icon <?php echo $selected_menu == 'creations' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/creations_icon.png" />
				Creations
			</a>
		</li>
		<li>
			<a href="<?php echo base_url()?>articles/welcome" class="about_icon <?php echo $selected_menu == 'about' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/about_icon.png" />
				About
			</a>
		</li>
		<?php if (isAdmin()) {?>
		<li>
			<a href="<?php echo base_url()?>admin" class="admin_icon <?php echo $selected_menu == 'admin' ? 'selected' : ''?>">
				<img src="<?php echo base_url()?>images/admin_icon.png" />
				Admin
			</a>
		</li>
		<?php }?>
	</menu>
</div>