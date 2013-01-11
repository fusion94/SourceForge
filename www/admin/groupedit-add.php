<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: groupedit-add.php,v 1.16 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";
require "account.php";
session_require(array('group'=>'1','admin_flags'=>'A'));

// ########################################################

if ($GLOBALS["Submit"]) {
	// check for valid group name
	if (!account_groupnamevalid($GLOBALS['form_unixgrp'])) {
		exit_error("Invalid Group Name",$GLOBALS['register_error']);
	}	

	if ($GLOBALS['group_idname']) {
	$newid = db_insertid(db_query("INSERT INTO groups (group_name,public,unix_group_name,http_domain,status) VALUES "
		. "('$GLOBALS[group_idname]',$GLOBALS[form_public],'$GLOBALS[form_unixgrp]'"
		. ",'$GLOBALS[form_unixgrp].sourceforge.net','$form_status')")); 
	} 
	session_redirect("/admin/groupedit.php?group_id=$newid");
} 

site_header(array('title'=>"Welcome to Project Alexandria"));
?>

<form action="groupedit-add.php" method="post">
<p>New descriptive group name:
<br><input type="text" name="group_idname">
<p>New unix group name:
<br><input type="text" name="form_unixgrp">
<br>Status
<SELECT name="form_status">
<OPTION value="A">Active
<OPTION value="P">Pending
<OPTION value="H">Holding
<OPTION value="D">Deleted
</SELECT>
<br>Public?
<SELECT name="form_public">
<OPTION value="1">Yes
<OPTION value="0">No
</SELECT>
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
