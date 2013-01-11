<?php

require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

// get current information
$res_grp = group_get_result($group_id);

if (db_numrows($res_grp) < 1) {
	exit_error("Invalid Group","That group could not be found.");
}

//must be a project admin
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

if ($func) {
	/*
		Make a change to the database
	*/
	if ($func=='rmproject') {
		/*
			remove a project from this portal
		*/
		$feedback .= ' Removed a Project ';
		db_query("DELETE FROM portal_projects WHERE portal_id='$group_id' AND group_id='$rm_id'");

	} else if ($func=='rmuser') {
		/*
			remove a user from this portal
		*/
		$feedback .= ' Removed a User ';
		db_query("DELETE FROM user_group WHERE group_id='$group_id' AND user_id='$rm_id' AND admin_flags <> 'A'");

	} else if ($func=='addproject') {
		/*
			Add a project to this portal
		*/
		$res_newgroup = db_query("SELECT group_id FROM groups WHERE unix_group_name='$form_unix_name'");

		if (db_numrows($res_newgroup) > 0) {
			//user was found
			$form_newuid = db_result($res_newgroup,0,'group_id');

			//if not already a member, add them
			$res_member = db_query("SELECT * FROM portal_projects WHERE group_id='$form_newuid' AND portal_id='$group_id'");
			if (db_numrows($res_member) < 1) {
				//not a member
				db_query("INSERT INTO portal_projects (group_id,portal_id) VALUES ('$form_newuid','$group_id')");
				$feedback .= " Project was added to this portal ";
			} else {
				//was a member
				$feedback .= " Project was already a member of this portal ";
			}
		} else {
			//user doesn't exist
			$feedback .= "That project does not exist on SourceForge";
		}

	} else if ($func=='adduser') {
		/*
			Add a user to this project
			They don't need unix access
		*/
		include ('account.php');
		account_add_user_to_group ($group_id,$form_unix_name);
/*
		$res_newuser = db_query("SELECT user_id FROM user WHERE user_name='$form_unix_name'");

		if (db_numrows($res_newuser) > 0) {
			//user was found
			$form_newuid = db_result($res_newuser,0,'user_id');

			//if not already a member, add them
			$res_member = db_query("SELECT user_id FROM user_group WHERE user_id='$form_newuid' AND group_id='$group_id'");
			if (db_numrows($res_member) < 1) {
				//user is not a member
				db_query("INSERT INTO user_group (user_id,group_id) VALUES ('$form_newuid','$group_id')");
				$feedback .= " User was added to this portal ";
			} else {
				//user was a member
				$feedback .= " User was already a member of this portal ";
			}
		} else {
			//user doesn't exist
			$feedback .= "That user does not exist on SourceForge";
		}
*/
	}
}


project_admin_header(array('title'=>"Project Admin: ".group_getname($group_id),'group'=>$group_id));

/*

	Show the list of member projects

*/

echo '<TABLE width=100% cellpadding=2 cellspacing=2 border=0>
<TR valign=top><TD width=50%>';

html_box1_top("Member Projects");

$sql="SELECT groups.group_name,groups.unix_group_name,groups.group_id ".
	"FROM groups,portal_projects ".
	"WHERE portal_projects.group_id=groups.group_id ".
	"AND portal_projects.portal_id='$group_id'";

$res_grp=db_query($sql);
$rows=db_numrows($res_grp);

if (!$res_grp || $rows < 1) {
	echo 'No Projects';
	echo db_error();
} else {
	for ($i=0; $i<$rows; $i++) {
		print '
		<FORM ACTION="'. $PHP_SELF .'" METHOD="POST"><INPUT TYPE="HIDDEN" NAME="func" VALUE="rmproject">'.
		'<INPUT TYPE="HIDDEN" NAME="rm_id" VALUE="'. db_result($res_grp,$i,'group_id') .'">'.
		'<TR><TD ALIGN="MIDDLE"><INPUT TYPE="IMAGE" NAME="DELETE" SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" BORDER="0"></TD></FORM>'.
		'<TD><A href="/projects/'. strtolower(db_result($res_grp,$i,'unix_group_name')) .'/">'. db_result($res_grp,$i,'group_name') .'</A></TD></TR>';
	}
}

html_box1_bottom();

echo '
</TD><TD>&nbsp;</TD><TD width=50%>';


/*

	Show the members of this project

*/

html_box1_top("Group Members");

$res_memb = db_query("SELECT user.realname,user.user_id ".
		"FROM user,user_group ".
		"WHERE user.user_id=user_group.user_id ".
		"AND user_group.group_id=$group_id");

	while ($row_memb=db_fetch_array($res_memb)) {
		print '
		<FORM ACTION="'. $PHP_SELF .'" METHOD="POST"><INPUT TYPE="HIDDEN" NAME="func" VALUE="rmuser">'.
		'<INPUT TYPE="HIDDEN" NAME="rm_id" VALUE="'.$row_memb['user_id'].'">'.
		'<TR><TD ALIGN="MIDDLE"><INPUT TYPE="IMAGE" NAME="DELETE" SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" BORDER="0"></TD></FORM>'.
		'<TD><A href="/developer/index.php?form_dev='.$row_memb['user_id'].'">'.$row_memb['realname'].'</A></TD></TR>';
	}

echo '
	<TR><TD colspan="2" align="center">
	&nbsp;<BR>
	<A href="/project/admin/userperms.php?group_id='. $group_id.'">[Edit Member Permissions]</A>';

html_box1_bottom();


echo '</TD></TR>

<TR valign=top><TD width=50%>';

/*

	Tool admin pages

*/

html_box1_top('Tool Admin');

echo '
<A HREF="/news/admin/?portal_id='.$group_id.'">News Admin</A><BR>
<A HREF="/forum/admin/?group_id='.$group_id.'">Forum Admin</A><BR>
';

html_box1_bottom();

echo '</TD>

<TD>&nbsp;</TD>

<TD width=50%>';

/*
	Add project/users
*/

html_box1_top('Add Projects/Users');

print '
	<FORM ACTION="'. $PHP_SELF .'" METHOD="POST">
	<TR><TD><B>Add Project:</B></TD><TD><INPUT TYPE="RADIO" NAME="func" VALUE="addproject" CHECKED></TR>
	<TR><TD><B>Add User:</B></TD><TD><INPUT TYPE="RADIO" NAME="func" VALUE="adduser"></TD></TR>
	<TR><TD><B>Unix Name:</B></TD><TD><INPUT TYPE="TEXT" NAME="form_unix_name" VALUE=""></TD></TR>
	<TR><TD COLSPAN="2" ALIGN="CENTER"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Add"></TD></TR></FORM>
';

html_box1_bottom();

echo '</TD>
</TR>
</TABLE>';

project_admin_footer(array());

?>
