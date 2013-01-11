<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.66 2000/07/03 15:31:21 tperdue Exp $

require "pre.php";    
session_require(array('isloggedin'=>'1'));
site_header(array('title'=>"Account Maintenance"));

// get global user vars
$res_user = db_query("SELECT * FROM user WHERE user_id=" . user_getid());
$row_user = db_fetch_array($res_user);

html_box1_top("Account Maintenance: " . user_getname()); ?>

<p>Welcome, <b><?php print user_getname(); ?></b>. 
<p>You can view/change all of your account features from here. You may also wish
to view your developer/consultant profiles and ratings.

<UL>
<LI><A href="/developer/?form_dev=<?php print user_getid(); ?>"><B>View My Developer Profile</B></A>
<LI><A HREF="/people/editprofile.php"><B>Edit My Skills Profile</B></A>
</UL>
<?php html_box1_bottom(); ?>

<TABLE width=100% cellpadding=0 cellspacing=0 border=0><TR valign=top>
<TD width=50%>

<?php html_box1_top("Personal Information"); ?>
&nbsp;<BR>
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>

<TR valign=top>
<TD>Member Since: </TD>
<TD><B><?php print date($sys_datefmt,$row_user['add_date']); ?></B></TD>
</TR>
<TR valign=top>
<TD>User ID: </TD>
<TD><B><?php print $row_user['user_id']; ?></B></TD>
</TR>

<TR valign=top>
<TD>Login Name: </TD>
<TD><B><?php print $row_user['user_name']; ?></B>
<BR><A href="change_pw.php">[Change Password]</A></TD>
</TR>

<TR valign=top>
<TD>Timezone: </TD>
<TD><B><?php print $row_user['timezone']; ?></B>
<BR><A href="change_timezone.php">[Change Timezone]</A></TD>
</TR>


<TR valign=top>
<TD>Real Name: </TD>
<TD><B><?php print $row_user['realname']; ?></B>
<BR><A href="change_realname.php">[Change Real Name]</A></TD>
</TR>

<TR valign=top>
<TD>Email Addr: </TD>
<TD><B><?php print $row_user['email']; ?></B>
<BR><A href="change_email.php">[Change Email Addr]</A>
</TD>
</TR>

<TR valign=top>
<TD>Skills Profile: </TD>
<TD><A href="/people/editprofile.php">[Edit Skills Profile]</A></TD>
</TR>

</TABLE>
<?php html_box1_bottom(); ?>

<?php html_box1_top("Group Info"); 
// now get listing of groups for that user
$res_cat = db_query("SELECT groups.group_name AS group_name, "
	. "groups.group_id AS group_id, "
	. "user_group.admin_flags AS admin_flags, "
	. "user_group.bug_flags AS bug_flags FROM "
	. "groups,user_group WHERE user_group.user_id=" . user_getid() . " AND "
	. "groups.group_id=user_group.group_id");

// see if there were any groups
if (db_numrows($res_cat) < 1) {
?>
<p>You are not currently a member of any project or consultant groups. If you
wish to participate in a project, please go to the individual project page for
information on how to help.
<?php
} else { // endif no groups
	print "<p>You are a member of the following groups:<BR>&nbsp;";
	while ($row_cat = db_fetch_array($res_cat)) {
		print ("<BR>" . "<A href=\"/project/?group_id=$row_cat[group_id]\">"
			. group_getname($row_cat['group_id']) . "</A>\n");
		print ("<I>(Developer");
		if (user_ismember($row_cat['group_id'],'A')) { print ", Admin"; }
		print (")</I>");
	}
	print "</ul>";
} // end if groups
html_box1_bottom(); ?>
</TD>
<TD>&nbsp;</TD>
<TD width=50%>
<?php 
// ############################# Preferences
html_box1_top("Preferences"); ?>
<FORM action="updateprefs.php" method="post">

<INPUT type="checkbox" name="form_mail_site" value="1"<?php 
	if ($row_user['mail_siteupdates']) print " checked"; ?>> Receive Email for Site Updates
<I>(This is very low traffic and will include security notices. Highly recommended.)</I>

<P><INPUT type="checkbox"  name="form_mail_va" value="1"<?php
	if ($row_user['mail_va']) print " checked"; ?>> Receive additional community mailings. 
<I>(Low traffic.)</I>

<P align=center><CENTER><INPUT type="submit" name="Update" value="Update"></CENTER>
</FORM>
<?php html_box1_bottom(); 

// ############################### Shell Account

if ($row_user[unix_status] == 'A') {
	html_box1_top("Shell Account Information"); 
	print '&nbsp;
<BR>Shell box: <b>'.$row_user[unix_box].'</b>
<BR>CVS/SSH Shared Keys: <B>';
	// get shared key count from db
	$expl_keys = explode("###",$row_user['authorized_keys']);
	if ($expl_keys[0]) {
		print (sizeof($expl_keys));
	} else {
		print '0';
	}
	print '</B> <A href="editsshkeys.php">[Edit Keys]</A>';
	html_box1_bottom(); 
} 
?>

</TD>
</TR></TABLE>

<?php
site_footer(array());
?>
