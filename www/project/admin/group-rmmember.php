<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: group-rmmember.php,v 1.12 2000/01/13 18:36:36 precision Exp $

require "pre.php";    

//FIXED
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

// make sure that user is not an admin
db_query("SELECT admin_flags FROM user_group WHERE user_id=$form_user AND group_id=$group_id");
$row_flags = db_fetch_array();

if (ereg("A",$row_flags[admin_flags],$ereg_match)) {
	exit_error("Error Removing Group Member","You cannot remove a group administrator. "
	. "Email <A href=\"mailto:project_admin@sourceforge.org\">project_admin@sourceforge.org</A> "
	. "to request a change in project administration.");
} 
	
db_query("DELETE FROM user_group WHERE user_id=$form_user AND group_id = $group_id");
session_redirect("/project/admin/index.php?group_id=$group_id");
?>
