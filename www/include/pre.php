<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: pre.php,v 1.345 2000/04/19 12:58:01 tperdue Exp $

/*
	redirect to proper hostname to get around certificate problem on IE 5
*/
if (($HTTP_HOST != 'sourceforge.net') && ($HTTP_HOST != 'webdev.sourceforge.net') && ($HTTP_HOST != 'prodigy') && ($HTTP_HOST != 'localhost')) {
	if ($SERVER_PORT == '443') {
		header ("Location: https://sourceforge.net$REQUEST_URI");
	} else {
		header ("Location: http://sourceforge.net$REQUEST_URI");
	}
	exit;
}

require('utils.php');
require('database.php');
require('html.php');
require('session.php');
require('user.php');
require('group.php');
require('exit.php');
require('menu.php');
require('error.php');
require('help.php');

$sys_datefmt = "m/d/y H:i";


// ###################################### header
// ###################################### functions

function generic_header($params) {

	// printable option
	if ($GLOBALS['printable']) {
		return;
	}
	
	global $G_USER, $G_SESSION;
	
	if (!$params['title']) { 
		$params['title'] = "SourceForge";
	} else {
		$params['title'] = "SourceForge: " . $params['title'];
	}
	?>
	<HTML>
	<HEAD>
	<TITLE><?php print $params['title']; ?></TITLE>
	<SCRIPT language="JavaScript">
	<!--
	function help_window(helpurl) {
		HelpWin = window.open(helpurl,'HelpWindow','scrollbars=yes,resizable=yes,toolbar=no,height=400,width=400');
	}
	// -->
	</SCRIPT>
	<LINK rel="stylesheet" href="/sourceforge.css" type="text/css">
	</HEAD>
	<BODY bgcolor=#FFFFFF topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
	<!-- top strip -->
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=2 bgcolor="<?php print $GLOBALS['COLOR_MENUBARBACK']; ?>">
	<TR>
	<TD><SPAN class=maintitlebar>&nbsp;&nbsp;
	<A class=maintitlebar href="/"><B>Home</B></A> | 
	<A class=maintitlebar href="/about.php"><B>About</B></A> | 
	<A class=maintitlebar href="/partners.php"><B>Partners</B></a> |
	<A class=maintitlebar href="/contact.php"><B>Contact Us</B></A> |
	<?php
		if (user_isloggedin()) {
			print '<A class=maintitlebar href="/account/logout.php"><B>Logout</B></A></SPAN>';
		} else {
			print '<A class=maintitlebar href="/account/login.php"><B>Login</B></A></SPAN>';
		}
	if (user_isloggedin()) { 

		?>
<!--		</TD><TD align=right><SPAN class=maintitlebar>
		Logged In: <B><?php print user_getname(); ?></B></SPAN> -->
		<?php 
	} 
	?>
	</TD>
	<td align="right"><A class=maintitlebar href="http://linux.com"><B>linux.com partner</B></a>&nbsp;</td>
	</TR>
	</TABLE>
	<!-- end top strip -->
	<!-- top title table -->
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=0 bgcolor="<?php echo $GLOBALS['COLOR_BARBACK']; ?>" valign="center">
	<TR valign="top" bgcolor="<?php echo $GLOBALS['COLOR_LTBACK1']; ?>"><TD>
	<A href="/"><?php 

	//html_image('sflogo2-105b.png',array(vspace=>0)); 
	html_image('sflogo2-steel.png',array('vspace'=>'0'));
	?></A>
	</TD>
	<TD width="99%"><!-- right of logo -->
	<a href="http://www.valinux.com"><?php html_image("valogo3.png",array('align'=>'right','alt'=>'VA Linux Systems','hspace'=>'5','vspace'=>'0')); ?></A>

	<BR>
	<FONT SIZE="+1">Breaking Down The Barriers to Open Source Development</FONT>
	<BR>
	<?php 
	if (!user_isloggedin()) {
 		print '<B>Status: Not Logged In</B>
			<A href="https://'.getenv('HTTP_HOST').'/account/login.php">[Login]</A> |
			<A href="https://'.getenv('HTTP_HOST').'/account/register.php">[New User]</A><BR>';
	}
	?>

	<A href="/softwaremap/">[Software Map]</A>
	<A href="/new/">[New Releases]</A>
	<A href="/docs/site/">[Site Docs]</A>
	<A href="/top/">[Top Projects]</A>

	<!-- VA Linux Stats Counter -->
 	<?php if (!session_issecure()) {
 	print '<IMG src="http://www2.valinux.com/clear.gif?id=105" width=1 height=1 alt="Counter">';
 	}
 	?>


	</TD><!-- right of logo -->
	</TR>

	<TR><TD bgcolor="#543a48" colspan=2><IMG src="/images/blank.gif" height=2 vspace=0></TD></TR>

	</TABLE>
	<!-- end top title table -->
	<?php
}

// ############################

function generic_footer($params) {

	// printable option
	if ($GLOBALS['printable']) {
		return;
	}

	global $IS_DEBUG,$QUERY_COUNT;
	if ($IS_DEBUG && user_ismember(1,'A')) {
		echo "<CENTER><B><FONT COLOR=RED>Query Count: $QUERY_COUNT</FONT></B></CENTER>";
		echo "<P>$GLOBALS[G_DEBUGQUERY]";
	}
	?>
	<!-- footer table -->
	<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="<?php print $GLOBALS['COLOR_MENUBARBACK']; ?>">
	      <tr>
		<td align="center"><font color="#ffffff"><span class="titlebar">
		All trademarks and copyrights on this page are properties of their respective owners. Forum comments are owned by the poster. The rest is copyright ©1999-2000 VA Linux Systems, Inc.
		</span></font></td>
      		</tr>
    	</table>
	<!-- end footer table -->
  	</body>
	</html>
	<?php
}

// ############################

function site_cleanup($params) {
	// function for any page cleanup later, no HTML
	return true;
	//@mysql_close($GLOBALS['conn']);
}

// ############################

function site_footer($params) {
	// printable option
	if ($GLOBALS['printable']) {
		return;
	}
	
	?>
	<!-- end content -->
	<p>&nbsp;</p>
	</td>
	<td width="9" bgcolor="#FFFFFF">
		<?php html_blankimage(1,10); ?>
	</td>

	</tr>
	</table>
	<?php
	generic_footer($params);
}

// ############################

function site_header($params) {
	generic_header($params); 
	// printable option
	if ($GLOBALS['printable']) {
		return;
	}
	
	?>
	<!-- content table -->
	<TABLE width="100%" cellspacing=0 cellpadding=0 border=0>
	<TR valign="top">
	<TD bgcolor=<?php print $GLOBALS['COLOR_MENUBACK']; ?>>
	<!-- menus -->
	<?php

	//sf global choices
	menu_main();

	//login / logged in menu
	if (user_isloggedin()) {
		echo menu_loggedin(); 
	} else {
		echo menu_notloggedin();
	}
	if ($params['group']) {
		echo menu_project($params['group']);
	}
	//search menu
	echo menu_search();

	?>
	</TD>

	<td width="9" bgcolor="#FFFFFF">
		<?php html_blankimage(1,9); ?>
	</td>
	<!-- content -->

	<td width="99%">
	&nbsp;<BR>
	<?php

}


// #######################################################
// ######################## Sitewide initialization

// #### Connect to db

db_connect();

if (!$conn) {
	exit_error("Could Not Connect to Database",db_error());
}

require('logger.php');

// #### set session

session_set();

?>
