<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: pre.php,v 1.317 2000/01/13 18:36:35 precision Exp $

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

$sys_datefmt = "m/d/y H:i";


// ###################################### header
// ###################################### functions

function generic_header($params) {

	// printable option
	if ($GLOBALS[printable]) {
		return;
	}
	
	global $G_USER, $G_SESSION;
	
	if (!$params[title]) { 
		$params[title] = "SourceForge";
	} else {
		$params[title] = "SourceForge: " . $params[title];
	}
	?>
	<HTML>
	<HEAD>
	<TITLE><?php print $params[title]; ?></TITLE>
	<LINK rel="stylesheet" href="/sourceforge.css" type="text/css">
	</HEAD>
	<BODY bgcolor=#FFFFFF topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
	<!-- top strip -->
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=2 bgcolor="<?php print $GLOBALS[COLOR_MENUBARBACK]; ?>">
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
		</TD><TD align=right><SPAN class=maintitlebar>
		Logged In: <B><?php print user_getname(); ?></B></SPAN>
		<?php 
	} 
	?>
	</TD>
	</TR>
	</TABLE>
	<!-- end top strip -->
	<!-- top title table -->
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=0 bgcolor="<?php echo $GLOBALS[COLOR_BARBACK]; ?>" valign="center">
	<TR valign="top" bgcolor="<?php echo $GLOBALS[COLOR_LTBACK1]; ?>"><TD>
        <A href="/"><?php 

	//html_image('sflogo2-105b.png',array(vspace=>0)); 
	html_image('sflogo2-steel.png',array('vspace'=>'0'));
	?></A>
        </TD>
        <TD width="99%"><!-- right of logo -->
	<a href="http://www.valinux.com"><?php html_image("va-btn-small-light.png",array('align'=>'right','alt'=>'VA Linux Systems','hspace'=>'5','vspace'=>'7')); ?></A>

	&nbsp;<BR><FONT size="+1"><B>SourceForge</B></FONT>
	<BR>Site Application Version: 1.0.3
	<P>
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

	<!-- VA Linux Stets Counter -->
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
	if ($GLOBALS[printable]) {
		return;
	}

	global $IS_DEBUG,$QUERY_COUNT;
	if ($IS_DEBUG) {
		echo "<CENTER><B><FONT COLOR=RED>Query Count: $QUERY_COUNT</FONT></B></CENTER>";
		echo "<P>$GLOBALS[G_DEBUGQUERY]";
	}
	?>
	<!-- footer table -->
	<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="<?php print $GLOBALS[COLOR_MENUBARBACK]; ?>">
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
	@mysql_close($GLOBALS[conn]);
}

// ############################

function site_footer($params) {
	// printable option
	if ($GLOBALS[printable]) {
		return;
	}
	
	?>
	<!-- end content -->
	<p>&nbsp;</p>
	</td>

	<td width="5" bgcolor="#ffffff">
	<?php html_blankimage(1,5); ?>
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
	if ($GLOBALS[printable]) {
		return;
	}
	
	?>
	<!-- content table -->
	<TABLE width="100%" cellspacing=0 cellpadding=0 border=0>
	<TR valign="top">
	<TD bgcolor=<?php print $GLOBALS[COLOR_MENUBACK]; ?>>
	<!-- menus -->
	<?php
	menu_main();

	if (user_isloggedin()) menu_loggedin(); 
	if (!user_isloggedin()) menu_notloggedin(); 
	// no longer printing proj menus
	// if ($params[group]) menu_project($params[group]);
	// if ($params[group] && (user_ismember($params[group]))) menu_projectdevel($params[group]);
	if ($params[group] && (user_ismember($params[group],'A'))) menu_projectadmin($params[group]);
	menu_search();
	if (user_ismember(1)) menu_admin(); 
	?>

	</TD>

	<td width="10" bgcolor="#ffffff">
	<?php html_blankimage(1,10); ?>
	</td>
	<!-- content -->

	<td width="99%"><BR>
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
