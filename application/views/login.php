<h1>Login</h1>

<form name="login_form" id="login_form" class="user_form normal_form clearleft" action="<?php echo base_url()?>user/loginsubmit" method="post">
	<label for="login_username">username:</label>
	<input type="text" id="login_username" name="username" maxlength="40" />
	<label for="login_password">password:</label>
	<input type="password" id="login_password" name="password" />
	<input type="submit" name="submit" value="log in" />
	<input type="hidden" name="return_url" value="<?php echo isset($return_url) ? $return_url : ''?>" />
</form>