<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: login.php,v 1.40 2000/07/13 21:13:10 tperdue Exp $

require ('pre.php');

/*

if (!session_issecure()) {
	//force use of SSL for login
	header('Location: https://'.$HTTP_HOST.'/account/login.php');
}

*/

// ###### first check for valid login, if so, redirect

if ($login && session_login_valid($form_loginname,$form_pw)) {
	if ($return_to) {
		session_redirect($return_to);
		exit;
	} else {
		session_redirect('/my/');
		exit;
	}
} else {
	
	site_header(array('title'=>'Login'));

	if ($login && error_is_error()) {
		
		if (error_get_string() == "Account Pending") {

			?>
			<P><B>Pending Account</B>

			<P>Your account is currently pending your email confirmation.
			Visiting the link sent to you in this email will activate your account.

			<P>If you need this email resent, please click below and a confirmation
			email will be sent to the email address you provided in registration.

			<P><A href="pending-resend.php?form_user=<?php print $form_loginname; ?>">[Resend
			Confirmation Email]</A>

			<br><hr>
			<p>


			<?php
		} else {
		
			echo '<h2><FONT COLOR="RED">'. error_get_string() .'</FONT></H2>';
		} //end else

	}

	if (browser_is_windows() && browser_is_ie() && browser_get_version() < '5.1') {
		echo '<H2><FONT COLOR="RED">Internet Explorer users need to
		upgrade to IE 5.01 or higher, preferably with 128-bit SSL or use Netscape 4.7 or higher</FONT></H2>';
	}

	if (browser_is_ie() && browser_is_mac()) {
		echo '<H2><FONT COLOR="RED">Internet Explorer on the Macintosh 
		is not supported currently. Use Netscape 4.7 or higher</FONT></H2>';
	}


	?>
	
	<p>
	<b>SourceForge Site Login</b>
	<p>
	<font color="red"><B>Cookies must be enabled past this point.</B></font>
	<P>
	<form action="https://<?php echo $HTTP_HOST; ?>/account/login.php" method="post">
	<INPUT TYPE="HIDDEN" NAME="return_to" VALUE="<?php echo $return_to; ?>">
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
