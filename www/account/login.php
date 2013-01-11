<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: login.php,v 1.25 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

if (!session_issecure()) {
	header('Location: https://'.$HTTP_HOST.'/account/login.php');
}

// ###### function login_valid()
// ###### checks for valid login from form post

function login_valid()	{
	global $HTTP_POST_VARS;

	if (!$HTTP_POST_VARS['form_loginname'] || !$HTTP_POST_VARS['form_pw']) {
		return false;
	}
	$res = db_query("SELECT user_id,status FROM user WHERE "
		. "user_name LIKE '$HTTP_POST_VARS[form_loginname]' "
		. "AND user_pw='" . md5($HTTP_POST_VARS[form_pw]) . "'");
	if (db_numrows($res) < 1) {
		return false;
	}

	// check status
	$usr = db_fetch_array($res);
	if ($usr[status] == 'S') session_redirect("/account/suspended.php");
	if ($usr[status] == 'P') session_redirect("/account/pending.php?form_user=$usr[user_id]");
	if ($usr[status] == 'D') session_redirect("/account/deleted.php");
	
	// if we got this far, the name/pw must be ok
	db_query("UPDATE session SET user_id=" . db_result($res,0,"user_id")
		. " WHERE session_hash='$GLOBALS[session_hash]'");
	return 1;
}

// ###### first check for valid login, if so, redirect

if ($form_loginname && $form_pw && login_valid()) {
	session_redirect("/my/");
} else {
	
	site_header(array('title'=>'Login'));

	if ($form_loginname || $form_pw) {
		echo '<h2><FONT COLOR="RED">Invalid Login Attempt</FONT></H2>';
	}

	?>

	<p>
	<b>SourceForge Site Login</b>
	<form action="login.php" method="post">
	<p>Login Name:
	<br><input type="text" name="form_loginname" VALUE="<?php echo $form_loginname; ?>">
	<p>Password:
	<br><input type="password" name="form_pw">
	<p><input type="submit" name="Login" value="Login">
	</form>

	<P>
	<A href="lostpw.php">[Lost your password?]</A>
	<P>
	<A HREF="register.php">[New Account]</A>

	<?php

}

site_footer(array());
site_cleanup(array());

?>
