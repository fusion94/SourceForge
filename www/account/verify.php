<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: verify.php,v 1.9 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

// ###### function login_valid()
// ###### checks for valid login from form post

function login_valid()	{
	global $HTTP_POST_VARS;

	if (!$HTTP_POST_VARS["form_loginname"]) {
		return 0;
	}
	$res = db_query("SELECT confirm_hash,user_id,status FROM user WHERE "
		. "user_name LIKE '$HTTP_POST_VARS[form_loginname]' "
		. "AND user_pw='" . md5($HTTP_POST_VARS[form_pw]) . "'");
	if (db_numrows($res) < 1) {
		$GLOBALS[error_msg] = 'Invalid login/password.';
		return 0;
	}

	// check status
	$usr = db_fetch_array($res);
	if ($usr[status] == 'S') session_redirect("/account/suspended.php");
	if ($usr[status] == 'D') session_redirect("/account/deleted.php");

	if (strcmp($GLOBALS[confirm_hash],$usr[confirm_hash])) {
		$GLOBALS[error_msg] = 'Invalid confirmation hash.';
		return 0;
	}
	
	// if we got this far, the name/pw must be ok
	db_query("UPDATE user SET status='A' WHERE user_id=$usr[user_id]");
	db_query("UPDATE session SET user_id=" . db_result($res,0,"user_id")
		. " WHERE session_hash='$GLOBALS[session_hash]'");

//	$message = "A new user has been verified.\n\n"
//		. "Login Name: $GLOBALS[form_loginname]\n";
//	mail("admin@sourceforge.net","SourceForge New User",$message,"From: admin@sourceforge.net");

	return 1;
}

// ###### first check for valid login, if so, redirect

if (login_valid()) {
	session_redirect("/account/first.php");
}

site_header(array('title'=>'Login'));

?>
<p><b>SourceForge Account Verification</b>
<P>In order to complete your registration, login now. Your account will
then be activated for normal logins.
<?php 
if ($GLOBALS[error_msg]) {
	print '<P><FONT color="#FF0000">'.$GLOBALS[error_msg].'</FONT>';
}
?>
<form action="verify.php" method="post">
<p>Login Name:
<br><input type="text" name="form_loginname">
<p>Password:
<br><input type="password" name="form_pw">
<INPUT type="hidden" name="confirm_hash" value="<?php print $confirm_hash; ?>">
<p><input type="submit" name="Login" value="Login">
</form>

<?php
site_footer(array());
site_cleanup(array());

?>
