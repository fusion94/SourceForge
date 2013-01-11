<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: group-addmember.php,v 1.18 2000/01/26 10:44:32 tperdue Exp $

require "pre.php";    
require "account.php";
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

if ($GLOBALS[Submit]) {
	// if no uid, try login name
	if (!$form_newuid) {
		$res_newuser = db_query("SELECT user_id FROM user WHERE user_name='$form_newlogin'");
		if (db_numrows($res_newuser) > 0) {
			$row_newuser = db_fetch_array();
			$form_newuid = $row_newuser[user_id];
		} else {
			exit_error('Unknown user','That username does not exist.');
		}
	}	

	// check to make sure user exists and is not already a member
	
	$res_exist = db_query("SELECT user_id FROM user WHERE user_id=$form_newuid");
	$res_member = db_query("SELECT user_id FROM user_group WHERE user_id=$form_newuid AND group_id=$group_id");

	if (db_numrows($res_exist) < 1) {
		exit_error("Unknown user","That user does not exist on SourceForge");
	}
	
	if ((db_numrows($res_exist)>0) && (db_numrows($res_member)<1)) {
		db_query("INSERT INTO user_group (user_id,group_id) VALUES ($form_newuid,$group_id)");
		
		// unix account if not one
		db_query("SELECT unix_uid,unix_status FROM user WHERE user_id=$form_newuid");
		$row_unix = db_fetch_array();
		if ($row_unix[unix_status] == "N") {
			if (!$row_unix[unix_uid]) {
				db_query("UPDATE user SET unix_uid=" . account_nextuid() . " WHERE user_id=$form_newuid");
			}
			db_query("UPDATE user SET unix_status='A' WHERE user_id=$form_newuid");
		}

		session_redirect("/project/admin/index.php?group_id=$group_id");
	}
}

project_admin_header(array('title'=>'Add Group Member','group'=>$group_id));
?>
<P>Add Group Member to Project: <B><?php html_a_group($group_id); ?></B>

<P><FORM action="group-addmember.php" method="post">
New member UID (must be a number!):
<BR><INPUT type="text" name="form_newuid">
<P><B>OR</B> New member login name:
<BR><INPUT type="text" name="form_newlogin">
<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<BR><INPUT type="submit" name="Submit" value="Submit">
</FORM>

<?php
project_admin_footer(array());
?>
