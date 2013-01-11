<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: pre.php,v 1.382 2000/07/12 21:01:40 tperdue Exp $

/*
	redirect to proper hostname to get around certificate problem on IE 5
*/

// Defines all of the Source Forge hosts, databases, etc.
// This needs to be loaded first becuase the lines below depend upon it.
require ('/etc/local.inc');

if (($HTTP_HOST != $GLOBALS['sys_default_domain']) && ($HTTP_HOST != 'localhost')) {
	if ($SERVER_PORT == '443') {
		header ("Location: https://".$GLOBALS['sys_default_domain']."$REQUEST_URI");
	} else {
		header ("Location: http://".$GLOBALS['sys_default_domain']."$REQUEST_URI");
	}
	exit;
}

//various html utilities
require('utils.php');

//database abstraction
require('database.php');

//various html libs like button bar
require('html.php');

//security library
require('session.php');

//user functions like get_name, logged_in, etc
require('user.php');

//group functions like get_name, etc
require('group.php');

//exit_error library
require('exit.php');

//left-hand nav library
require('menu.php');

//library to determine/set/get error flags
require('error.php');

//library to set up context help
require('help.php');

//library to determine browser settings
require('browser.php');

$sys_datefmt = "Y-M-d H:i";


// ###################################### header
// ###################################### functions

function generic_header($params) {

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
			<A href="/account/login.php">[Login]</A> |
			<A href="/account/register.php">[New User]</A><BR>';
	}
	?>

	<A href="/softwaremap/">[Software Map]</A>
	<A href="/new/">[New Releases]</A>
	<A href="/docs/site/">[Site Docs]</A>
	<A href="/top/">[Top Projects]</A>

	<!-- VA Linux Stats Counter -->
 	<?php 
	if (!session_issecure()) {
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

function site_footer($params) {
	
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
	
	?>
	<!-- content table -->
	<TABLE width="100%" cellspacing=0 cellpadding=0 border=0>
	<TR valign="top">
	<TD bgcolor=<?php print $GLOBALS['COLOR_MENUBACK']; ?>>
	<!-- menus -->
	<?php

	/*
		See if this is a project or a portal
		and show the correct nav menus
	*/
	if ($params['group'] && group_get_type_id($params['group']) == 1) {
		//this is a project page
		//sf global choices
		echo menu_software();
		echo menu_sourceforge();
		echo menu_project ($params['group']);
	} else if ($params['group']) {
		//this is a portal page
		echo menu_portal_projects ($params['group']);
		echo menu_portal_guides($params['group']);
		echo menu_portal ($params['group']);
	} else {
		echo menu_software();
		echo menu_sourceforge();
	}

	//login / logged in menu
	if (user_isloggedin()) {
		echo menu_loggedin($params['title']);
	} else {
		echo menu_notloggedin();
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

} //end funtion site_header


// #### Connect to db

db_connect();

if (!$conn) {
	exit_error("Could Not Connect to Database",db_error());
}

//insert this page view into the database
require('logger.php');

// #### set session

session_set();

//set up the user's timezone if they are logged in
if (user_isloggedin()) {
	putenv('TZ='.user_get_timezone());
} else {
	//just use pacific time as always
}

?>
