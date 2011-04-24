
<div id="footer"></div>

</div>

<script type="text/javascript" src="<?php echo base_url()?>js/includes.js"></script> 
<?php if (isAdmin() && $this->router->class == 'admin') : ?>
<script type="text/javascript" src="<?php echo base_url()?>js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>js/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>js/prettify.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>js/jquery.syntaxhighlighter.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>js/jquery.filepicker.js"></script>

<script src="http://yui.yahooapis.com/2.8.2r1/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script src="http://yui.yahooapis.com/2.8.2r1/build/element/element-min.js"></script> 
<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script src="http://yui.yahooapis.com/2.8.2r1/build/container/container_core-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.2r1/build/menu/menu-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.2r1/build/button/button-min.js"></script>
<!-- Source file for Rich Text Editor-->
<script src="http://yui.yahooapis.com/2.8.2r1/build/editor/editor-min.js"></script>

<script type="text/javascript" src="<?php echo base_url()?>js/admin.js"></script>
<?php endif?>
<script type="text/javascript" src="<?php echo base_url()?>js/janhartigan.js"></script>
<?php if (!empty($js_files)) : ?>
	<?php foreach ($js_files as $file) : ?>
		<script type="text/javascript" src="<?php echo base_url()?>js/<?php echo $file?>"></script>
	<?php endforeach?>
<?php endif?>
<?php if (isset($disqus) && !empty($disqus)) {?>
	<script type="text/javascript">
		$(function() {
	    	var disqus_shortname = 'janhartigan';
		
		    (function() {
		        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
		        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		    })();
		});
	</script>
<?php }?>
<script type="text/javascript">
  var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-21898002-1']);_gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>