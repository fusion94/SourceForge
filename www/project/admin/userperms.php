<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: userperms.php,v 1.29 2000/01/13 18:36:36 precision Exp $

require "pre.php";    

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) exit_error("Invalid Group","That group does not exist.");
$row_grp = db_fetch_array($res_grp);

// ########################### form submission, make updates
if ($GLOBALS[submit]) {
	$res_dev = db_query("SELECT user_id FROM user_group WHERE group_id=$group_id");
	while ($row_dev = db_fetch_array($res_dev)) {
		eval("\$bug_flags=\"\$bugs_user_$row_dev[user_id]\";");
		eval("\$forum_flags=\"\$forums_user_$row_dev[user_id]\";");
		eval("\$project_flags=\"\$projects_user_$row_dev[user_id]\";");
		$res = db_query('UPDATE user_group SET ' 
			."bug_flags=$bug_flags,"
			."forum_flags=$forum_flags,"
			."project_flags=$project_flags "
			."WHERE user_id=$row_dev[user_id] AND group_id=$group_id");
		if (!$res) $query_error = 1;
	}
}

if ($query_error) {
	exit_error('Query Error','There was an unknown query error. Please email
admin@sourceforge.net with details of this problem.');
}

$res_dev = db_query("SELECT user.user_name AS user_name,"
	. "user.user_id AS user_id,"
	. "user_group.admin_flags AS admin_flags,"
	. "user_group.bug_flags AS bug_flags,"
	. "user_group.forum_flags AS forum_flags,"
	. "user_group.project_flags AS project_flags "
	. "FROM user,user_group WHERE "
	. "user.user_id=user_group.user_id AND user_group.group_id=$group_id "
	. "ORDER BY user.user_name");

site_header(array(title=>'Project Developer Permissions',group=>$group_id));
?>

<P><B>Developer Permissions for Project: <?php html_a_group($group_id); ?></B>
<?php
	if ($GLOBALS[submit]) {
		print '<P><FONT color="#FF0000">Update successful.</FONT>';
	}
?>
<P>
<FORM action="userperms.php" method="post">
<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<TABLE width="100%" cellspacing=0 cellpadding=0 border=0>
<TR><TD><B>Developer Name</B></TD>
<TD><B>Admin?</B></TD>
<TD><B>CVS Write</B></TD>
<TD><B>Bug Tracking</B></TD>
<TD><B>Forums</B></TD>
<TD><B>Task Manager</B></TD>
</TR>

<?php
while ($row_dev = db_fetch_array($res_dev)) {
	html_colored_tr(" valign=middle");
	print '<TD>'.$row_dev[user_name].'</TD>';
	print '<TD>'.(($row_dev[admin_flags]=='A')?"Yes":"No").'</TD>';
	print '<TD>Yes</TD>';
	// bug selects
	print '<TD><FONT size="-1"><SELECT name="bugs_user_'.$row_dev[user_id].'">';
	print '<OPTION value="0"'.(($row_dev[bug_flags]==0)?" selected":"").'>None';
	print '<OPTION value="1"'.(($row_dev[bug_flags]==1)?" selected":"").'>Tech Only';
	print '<OPTION value="2"'.(($row_dev[bug_flags]==2)?" selected":"").'>Tech & Admin';
	print '<OPTION value="3"'.(($row_dev[bug_flags]==3)?" selected":"").'>Admin Only';
	print '</SELECT></FONT></TD>
';
	// forums
	print '<TD><FONT size="-1"><SELECT name="forums_user_'.$row_dev[user_id].'">';
	print '<OPTION value="0"'.(($row_dev[forum_flags]==0)?" selected":"").'>None';
	print '<OPTION value="2"'.(($row_dev[forum_flags]==2)?" selected":"").'>Moderator';
	print '</SELECT></FONT></TD>
';
	// project selects
	print '<TD><FONT size="-1"><SELECT name="projects_user_'.$row_dev[user_id].'">';
	print '<OPTION value="0"'.(($row_dev[project_flags]==0)?" selected":"").'>None';
	print '<OPTION value="1"'.(($row_dev[project_flags]==1)?" selected":"").'>Tech Only';
	print '<OPTION value="2"'.(($row_dev[project_flags]==2)?" selected":"").'>Tech & Admin';
	print '<OPTION value="3"'.(($row_dev[project_flags]==3)?" selected":"").'>Admin Only';
	print '</SELECT></FONT></TD>
';

	print '</TR>
';
}
?>

</TABLE>
<P align="center"><INPUT type="submit" name="submit" value="Update Developer Permissions">
</FORM>

<P><A href="/project/admin/?group_id=<?php print $group_id; ?>">[Return to Project Admin]</A>

<?php
site_footer(array());
site_cleanup(array());
?>
