<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="og:title" content="<?php echo $window_title?>" />
<meta name="og:type" content="website" />
<meta name="og:url" content="<?php echo curr_url()?>" />
<meta name="og:sitename" content="janhartigan.com" />
<meta name="og:description" content="<?php echo $window_description?>" />
<meta name="robots" content="index, follow" />
<meta name="keywords" content="programming, tech, javascript, jquery, php, python, css, html, web, math, animation, Jan Hartigan" />
<meta name="description" content="<?php echo $window_description?>" />
<?php if ($window_image) {?>
<meta name="og:image" content="<?php echo base_url().$window_image?>" />
<?php }?>
<title><?php echo $window_title?></title>
<link rel="Shortcut Icon" href="<?php echo base_url()?>favicon.ico"> 
<link href="<?php echo base_url()?>css/includes.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url()?>css/janhartigan.css" rel="stylesheet" type="text/css"/>
<?php if (isAdmin() && $this->router->class == 'admin') : ?>
<link href="<?php echo base_url()?>css/admin.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url()?>css/ui-lightness/jquery-ui-1.8.5.custom.css" />
<link href="<?php echo base_url()?>css/ui.jqgrid.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url()?>css/jquery.filepicker.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.2r1/build/assets/skins/sam/skin.css">
<?php endif?>

<?php if (!empty($css_files)) : ?>
	<?php foreach ($css_files as $file) : ?>
		<link href="<?php echo base_url()?>css/<?php echo $file?>" rel="stylesheet" type="text/css"/>
	<?php endforeach?>
<?php endif?>

</head>

<body>

<div id="mainwrapper">
	<div id="header">
		<a href="<?php echo base_url()?>" id="logo" title="janhartigan.com home"></a>
		<div class="clear"></div>
		<div id="site_nav_menu">
			<menu>
				<li>
					<a href="<?php echo base_url()?>" class="home_icon">
						<img src="<?php echo base_url()?>images/home_icon.png" />
						Home
					</a>
				</li>
				<li>
					<a href="<?php echo base_url()?>articles" class="blog_icon">
						<img src="<?php echo base_url()?>images/blog_icon.png" />
						Articles
					</a>
				</li>
				<li>
					<a href="<?php echo base_url()?>portfolio" class="portfolio_icon">
						<img src="<?php echo base_url()?>images/portfolio_icon.png" />
						Portfolio
					</a>
				</li>
				<li>
					<a href="<?php echo base_url()?>creations" class="creations_icon">
						<img src="<?php echo base_url()?>images/creations_icon.png" />
						Creations
					</a>
				</li>
				<li>
					<a href="<?php echo base_url()?>articles/welcome" class="about_icon">
						<img src="<?php echo base_url()?>images/about_icon.png" />
						About
					</a>
				</li>
				<?php if (isAdmin()) {?>
				<li>
					<a href="<?php echo base_url()?>admin" class="admin_icon">
						<img src="<?php echo base_url()?>images/admin_icon.png" />
						Admin
					</a>
				</li>
				<?php }?>
			</menu>
		</div>
		<div id="site_description_box">
			I am a programmer, economist, multilinguist (of both human and computer languages), and general 
			life-liver extraordinaire. Here you will find blog posts on a wide range of subjects and creations that typically focus 
			on web development. Despite this, I try to use programming as a tool to solve problems in other fields.
		</div>
		<div class="clear"></div>
	</div>
