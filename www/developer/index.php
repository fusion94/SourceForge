<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.19 2000/04/25 13:44:32 tperdue Exp $

/*
	Developer Info Page
	Written by dtype Oct 1999
*/

require ('pre.php');

site_header(array('title'=>'Developer Profile'));

// get global user vars
$res_user = db_query("SELECT * FROM user WHERE user_id=$form_dev");
$row_user = db_fetch_array($res_user);

html_box1_top("Developer Profile: " . $row_user['user_name']); 

?>
<p>
This is a public developer profile. Emails are in this format to
minimize spamming to these accounts.

<?php html_box1_bottom(); ?>

<TABLE width=100% cellpadding=0 cellspacing=0 border=0><TR valign=top>
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
		?>"><?php print $row_user['user_name']; ?> at users.sourceforge.net</A></B>
	</TD>
</TR>
<TR valign=top>
        <TD COLSPAN="2">
        <TD><A HREF="/people/viewprofile.php?user_id=<?php print $row_user['user_id']; ?>"><B>Skills Profile</B></A></TD>
</TR>

</TABLE>
<?php html_box1_bottom(); ?>

<?php html_box1_top("Group Info"); 
// now get listing of groups for that user
$res_cat = db_query("SELECT groups.group_name AS group_name, "
	. "groups.group_id AS group_id, "
	. "user_group.admin_flags AS admin_flags, "
	. "user_group.bug_flags AS bug_flags FROM "
	. "groups,user_group WHERE user_group.user_id=" . $form_dev . " AND "
	. "groups.group_id=user_group.group_id AND groups.public=1");

// see if there were any groups
if (db_numrows($res_cat) < 1) {
	?>
	<p>This developer is not a member of any project or consultant groups.
	<?php
} else { // endif no groups
	print "<p>This developer is a member of the following groups:<BR>&nbsp;";
	while ($row_cat = db_fetch_array($res_cat)) {
		print ("<BR>" . "<A href=\"/project/?group_id=$row_cat[group_id]\">"
			. group_getname($row_cat['group_id']) . "</A>\n");
	}
	print "</ul>";
} // end if groups

html_box1_bottom(); ?>
</TD>
<TD>&nbsp;</TD>
<TD width=50%>
<?php html_box1_top("Usage Statistics"); ?>
&nbsp;<BR>
Site Member Since: <B><?php print date("M d, Y",$row_user['add_date']); ?></B>
<?php html_box1_bottom(); ?>

</TD>
</TR></TABLE>

<?php
site_footer(array());
?>
