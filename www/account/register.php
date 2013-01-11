<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: register.php,v 1.44 2000/04/24 10:31:10 tperdue Exp $

require "pre.php";    
require "account.php";

// ###### function register_valid()
// ###### checks for valid register from form post

function register_valid()	{
	global $HTTP_POST_VARS, $G_USER;

	if (db_numrows(db_query("SELECT user_id FROM user WHERE "
		. "user_name LIKE '$HTTP_POST_VARS[form_loginname]'")) > 0) {
		$GLOBALS['register_error'] = "That username already exists.";
		return 0;
	}
	if (!$HTTP_POST_VARS['form_loginname']) {
		$GLOBALS['register_error'] = "You must supply a username.";
		return 0;
	}
	if (!$HTTP_POST_VARS['form_pw']) {
		$GLOBALS['register_error'] = "You must supply a password.";
		return 0;
	}
	if ($HTTP_POST_VARS['form_pw'] != $HTTP_POST_VARS['form_pw2']) {
		$GLOBALS['register_error'] = "Passwords do not match.";
		return 0;
	}
	if (!account_pwvalid($HTTP_POST_VARS['form_pw'])) {
		return 0;
	}
	if (!account_namevalid($HTTP_POST_VARS['form_loginname'])) {
		return 0;
	}
	
	// if we got this far, it must be good
	$confirm_hash = substr(md5($session_hash . $HTTP_POST_VARS['form_pw'] . time()),0,16);

	$result=db_query("INSERT INTO user (user_name,user_pw,unix_pw,realname,email,add_date,"
		. "status,confirm_hash,mail_siteupdates,mail_va) "
		. "VALUES ('$HTTP_POST_VARS[form_loginname]','"
		. md5($HTTP_POST_VARS['form_pw']) . "','"
		. account_genunixpw($HTTP_POST_VARS['form_pw']) . "','"
		. "$GLOBALS[form_realname]','$GLOBALS[form_email]'," . time() . ","
		. "'P','" // status
		. $confirm_hash
		. "',"
		. ($GLOBALS['form_mail_site']?"1":"0") . ","
		. ($GLOBALS['form_mail_va']?"1":"0") . ")");

	$GLOBALS['newuserid'] = db_insertid($result);

	// send mail
	$message = "Thank you for registering on the SourceForge web site. In order\n"
		. "to complete your registration, visit the following url: \n\n"
		. "https://sourceforge.net/account/verify.php?confirm_hash=$confirm_hash\n\n"
		. "Enjoy the site.\n\n"
		. " -- the SourceForge staff\n";

	mail($GLOBALS['form_email'],"SourceForge Account Registration",$message,"From: noreply@sourceforge.net");

	return 1;
}

// ###### first check for valid login, if so, congratulate

if ($Register && register_valid()) {

	site_header(array('title'=>'SourceForge: Register Confirmation'));
	?>
	<p><b>SourceForge: New Account Registration Confirmation</b>
	<p>Congratulations. You have registered on SourceForge.
	Your new username is: <b><?php print user_getname($GLOBALS['newuserid']); ?></b>

	<p>You are now being sent a confirmation email to verify your email 
	address. Visiting the link sent to you in this email will activate
	your account.

	<p>You should now <a href="/">Return to SourceForge</a>.
	<?php

} else { // not valid registration, or first time to page

	site_header(array('title'=>'SourceForge: Register'));

	?>
	<p><b>SourceForge New Account Registration</b>
	<?php if ($register_error) print "<p><FONT color=#FF0000>$register_error</FONT>"; ?>
	<form action="register.php" method="post">
	<p>Login Name:
	<br><input type="text" name="form_loginname">
	<p>Password:
	<br><input type="password" name="form_pw">
	<p>Password (repeat):
	<br><input type="password" name="form_pw2">
	<P>Full/Real Name:
	<BR><INPUT size=30 type="text" name="form_realname">
	<P>Email Address:
	<BR><I>This email address will be verified before account activation.
	It will not be displayed on the site. You will receive a mail forward
	account at loginname@users.sourceforge.net that will forward to
	this address.</I>
	<BR><INPUT size=30 type="text" name="form_email">
	<P><INPUT type="checkbox" name="form_mail_site" value="1" checked>
	Receive Email about Site Updates <I>(Very low traffic and includes
	security notices. Highly Recommended.)</I>
	<P><INPUT type="checkbox" name="form_mail_va" value="1">
	Receive additional community mailings. <I>(Low traffic.)</I>

	<p><input type="submit" name="Register" value="Register">
	</form>

	<?php
}

site_footer(array());
?>
