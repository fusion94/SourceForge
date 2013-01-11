<?php
// $Id: test.php,v 1.6 1999/12/29 19:11:36 dtype Exp $
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
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=2>
	<TR bgcolor="#000000">
	<TD><SPAN class=maintitlebar>&nbsp;&nbsp;
	<A class=maintitlebar href="/about.php"><B>About</B></A> | <A class=maintitlebar href="/contact.php"><B>Contact Us</B></A>
	</SPAN>
	</TD>

	<?php 

	if (user_isloggedin()) { 

		?>
		<TD align=right>
		<SPAN class=maintitlebar>
		Logged In: <B><?php print user_getname(); ?></B>
		&nbsp;&nbsp;</SPAN></TD>
		<?php 
	} 

	?>

	</TR>
	</TABLE>
	<!-- end top strip -->
	<!-- top title table -->
	<TABLE width="100%" border=0 cellspacing=0 cellpadding=0 bgcolor="<?php echo $GLOBALS[COLOR_BARBACK]; ?>" valign="center">
	<TR><TD background="/images/binary-code.gif">
	<a href="http://www.valinux.com"><?php html_image("va-btn-small-light.png",array('align'=>'right','alt'=>'VA Linux Systems','hspace'=>'5','vspace'=>'7')); ?></A>
	<A href="/"><?php 
			html_image("anvil.gif",array('vspace'=>'5','hspace'=>'5')); 
			html_image("sourceforge.gif",array('alt'=>'SourceForge','vspace'=>'10','hspace'=>'0')); 
	?></A>
	</TD></TR>
	<TR><TD bgcolor="#000000"><?php html_blankimage(4,1); ?></TD></TR>
	<tr>
	<td bgcolor="<?php echo $GLOBALS[COLOR_MENUBACK]; ?>"><?php html_blankimage(2,1); ?></td>
	</tr>

	<tr>
	<td bgcolor="#000000"><?php html_blankimage(2,1); ?></td>
	</tr>
	</TABLE>
	<!-- end top title table -->
	<?php
}

// ############################

function generic_footer($params) {
	global $IS_DEBUG,$QUERY_COUNT;
	if ($IS_DEBUG) {
		echo "<CENTER><B><FONT COLOR=RED>Query Count: $QUERY_COUNT</FONT></B></CENTER>";
		echo "<P>$GLOBALS[G_DEBUGQUERY]";
	}
	?>
	<!-- footer table -->
	<table width="100%" border="0" cellspacing="0" cellpadding="2" bgcolor="#000000">
	      <tr>
		<td align="center"><font color="#ffffff"><span class="titlebar">
		SourceForge is copyright (c)1999 VA Linux Systems. While in beta,
		all rights are reserved.</span></font></td>
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
	if ($params[group] && (user_ismember($GLOBALS[group_id]))) menu_projectdevel($params[group]);
	if ($params[group] && (user_ismember($GLOBALS[group_id],'A'))) menu_projectadmin($params[group]);
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

// #### set session

session_set();

?>
