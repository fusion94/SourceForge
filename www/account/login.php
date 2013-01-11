<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: login.php,v 1.30 2000/04/14 17:53:41 tperdue Exp $

require ('pre.php');

if (!session_issecure()) {
	//force use of SSL for login
	header('Location: https://'.$HTTP_HOST.'/account/login.php');
}

// ###### first check for valid login, if so, redirect

if ($login && session_login_valid($form_loginname,$form_pw)) {
	session_redirect('/my/');
} else {
	
	site_header(array('title'=>'Login'));

	if ($login && error_is_error()) {
		echo '<h2><FONT COLOR="RED">'. error_get_string() .'</FONT></H2>';
	}

	?>
	
	<p>
	<b>SourceForge Site Login</b>
	<p>
	<font color="red"><B>Cookies must be enabled past this point.</B></font>
	<P>
	<form action="login.php" method="post">
	<p>
	Login Name:
	<br><input type="text" name="form_loginname" VALUE="<?php echo $form_loginname; ?>">
	<p>
	Password:
	<br><input type="password" name="form_pw">
	<p>
	<input type="submit" name="login" value="Login">
	</form>

	<P>
	<A href="lostpw.php">[Lost your password?]</A>
	<P>
	<A HREF="register.php">[New Account]</A>

	<?php

}

site_footer(array());

?>
