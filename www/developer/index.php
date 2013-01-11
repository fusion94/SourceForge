<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.30 2000/07/12 21:01:40 tperdue Exp $

/*
	Developer Info Page
	Written by dtype Oct 1999
*/

require ('pre.php');

site_header(array('title'=>'Developer Profile'));

$res_user = db_query("SELECT * FROM user WHERE user_id=$form_dev");

if (!$res_user || db_numrows($res_user) < 1) {
	exit_error('No Such User','No Such User');
}

$row_user = db_fetch_array($res_user);

?>

<H3>Developer Profile</H3>
<P>
<TABLE width=100% cellpadding=2 cellspacing=2 border=0><TR valign=top>
<TD width=50%>

<?php html_box1_top("Personal Information"); ?>
&nbsp;
<BR>
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR valign=top>
	<TD>User ID: </TD>
	<TD><B><?php print $row_user['user_id']; ?></B></TD>
</TR>
<TR valign=top>
	<TD>Login Name: </TD>
	<TD><B><?php print $row_user['user_name']; ?></B></TD>
</TR>
<TR valign=top>
	<TD>Real Name: </TD>
	<TD><B><?php print $row_user['realname']; ?></B></TD>
</TR>
<TR valign=top>
	<TD>Email Addr: </TD>
	<TD>
	<B><A HREF="/sendmessage.php?touser=<?php print $row_user['user_id']; 
		?>"><?php print $row_user['user_name']; ?> at <?php print $GLOBALS['sys_users_host']; ?></A></B>
	</TD>
</TR>
<TR valign=top>
        <TD COLSPAN="2">
        <A HREF="/people/viewprofile.php?user_id=<?php print $row_user['user_id']; ?>"><B>Skills Profile</B></A></TD>
</TR>

<TR>
	<TD>
	Site Member Since: 
	</TD>
	<TD><B><?php print date("M d, Y",$row_user['add_date']); ?></B></TD>
</TR>

</TABLE>
<?php html_box1_bottom(); ?>

</TD>
<TD>&nbsp;</TD>
<TD width=50%>
<?php html_box1_top("Group Info"); 
// now get listing of groups for that user
$res_cat = db_query("SELECT groups.group_name, "
	. "groups.unix_group_name, "
	. "groups.group_id, "
	. "user_group.admin_flags, "
	. "user_group.bug_flags FROM "
	. "groups,user_group WHERE user_group.user_id=" . $form_dev . " AND "
	. "groups.group_id=user_group.group_id AND groups.is_public=1 AND groups.status='A'");

// see if there were any groups
if (db_numrows($res_cat) < 1) {
	?>
	<p>This developer is not a member of any project or consultant groups.
	<?php
} else { // endif no groups
	print "<p>This developer is a member of the following groups:<BR>&nbsp;";
	while ($row_cat = db_fetch_array($res_cat)) {
		print ("<BR>" . "<A href=\"/projects/$row_cat[unix_group_name]/\">$row_cat[group_name]</A>\n");
	}
	print "</ul>";
} // end if groups

html_box1_bottom(); ?>
</TD></TR>

<TR><TD COLSPAN="3">

<?php 

if (user_isloggedin()) {

	?>
	&nbsp;
	<P>
	<H3>Send a Message to <?php echo $row_user['realname']; ?></H3>
	<P>
	<FORM ACTION="/sendmessage.php" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="touser" VALUE="<?php echo $form_dev; ?>">

	<B>Your Email Address:</B><BR>
	<B><?php echo user_getname().'@'.$GLOBALS[sys_users_host]; ?></B>
	<INPUT TYPE="HIDDEN" NAME="email" VALUE="<?php echo user_getname().'@'.$GLOBALS['sys_users_host']; ?>">
	<P>
	<B>Your Name:</B><BR>
	<B><?php 

	$my_name=user_getrealname(user_getid());

	echo $my_name; ?></B>
	<INPUT TYPE="HIDDEN" NAME="name" VALUE="<?php echo $my_name; ?>">
	<P>
	<B>Subject:</B><BR>
	<INPUT TYPE="TEXT" NAME="subject" SIZE="30" MAXLENGTH="40" VALUE="">
	<P>
	<B>Message:</B><BR>
	<TEXTAREA NAME="body" ROWS="15" COLS="60" WRAP="HARD"></TEXTAREA>
	<P>
	<CENTER>
	<INPUT TYPE="SUBMIT" NAME="send_mail" VALUE="Send Message">
	</CENTER>
	</FORM>
	<?php

} else {

	echo '<H3>You Could Send a Message if you were logged in</H3>';

}

?>

</TD></TR>
</TABLE>

<?php
site_footer(array());

?>
