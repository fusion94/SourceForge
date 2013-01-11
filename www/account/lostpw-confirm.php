<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: lostpw-confirm.php,v 1.8 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

$confirm_hash = md5($session_hash . time());

$res_user = db_query("SELECT * FROM user WHERE user_name='$form_loginname'");
if (db_numrows($res_user) < 1) exit_error("Invalid User","That user does not exist on SourceForge.");
$row_user = db_fetch_array($res_user);

db_query("UPDATE user SET confirm_hash='$confirm_hash' WHERE user_id=$row_user[user_id]");

$message = "Someone (presumably you) on the SourceForge site requested a\n"
	. "password change through email verification. If this was not you,\n"
	. "ignore this message and nothing will happen.\n\n"
	. "If you requested this verification, visit the following URL\n"
	. "to change your password:\n\n"
	. "https://sourceforge.net/account/lostlogin.php?confirm_hash=$confirm_hash\n\n"
	. " -- the SourceForge staff\n";

mail ($row_user[email],"SourceForge Verification",$message,"From: admin@sourceforge.net");

session_securitylog("lostpw","User #$row_user[user_id] requested lost pw confirm_hash");

site_header(array(title=>"Lost Password Confirmation"));
?>

<P><B>Confirmation mailed</B>

<P>An email has been sent to the address you have on file. Follow
the instructions in the email to change your account password.

<P><A href="/">[Return to SourceForge]</A>

<?php
site_footer(array());
site_cleanup(array());
?>
