<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: lostlogin.php,v 1.10 2000/01/13 18:36:34 precision Exp $

require "pre.php";    
require "account.php";

// ###### function register_valid()
// ###### checks for valid register from form post

$res_lostuser = db_query("SELECT * FROM user WHERE confirm_hash='$confirm_hash'");
if (db_numrows($res_lostuser) > 1) {
	exit_error("Error","This confirm hash exists more than once.");
}
if (db_numrows($res_lostuser) < 1) {
	exit_error("Error","Invalid confirmation hash.");
}
$row_lostuser = db_fetch_array($res_lostuser);

if ($GLOBALS[Update] && $form_pw && !strcmp($form_pw,$form_pw2)) {
	db_query("UPDATE user SET "
		. "user_pw='" . md5($form_pw) . "',"
		. "unix_pw='" . account_genunixpw($form_pw) . "' WHERE "
		. "confirm_hash='$confirm_hash'");

	session_securitylog("lostpw","User #$row_lostuser[user_id] successfully changed password via lostpw confirm_hash");

	session_redirect("/");
}

site_header(array(title=>"Lost Password Login"));
?>
<p><b>Lost Password Login</b>
<P>Welcome, <?php print $row_lostuser[user_name]; ?>. You may now
change your password. Doing so by this method
will strip you of any administrative
priveleges you may have. Re-request admin status via email
to admin@sourceforge.net.

<FORM action="lostlogin.php">
<p>New Password:
<br><input type="password" name="form_pw">
<p>New Password (repeat):
<br><input type="password" name="form_pw2">
<input type="hidden" name="confirm_hash" value="<?php print $confirm_hash; ?>">
<p><input type="submit" name="Update" value="Update">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
