<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: task.php,v 1.8 2000/01/13 18:36:36 precision Exp $

require('pre.php');
require('../pm/pm_utils.php');

if ($group_id && $group_project_id) {
	/*
		Verify that this group_project_id falls under this group
	*/

	//can this person view these tasks? they may have hacked past the /pm/index.php page
	if (user_isloggedin() && user_ismember($group_id)) {
		$public_flag='0,1';
	} else {
		$public_flag='1';
	}

	$result=db_query("SELECT * FROM project_group_list ".
		"WHERE group_project_id='$group_project_id' AND group_id='$group_id' AND is_public IN ($public_flag)");
	if (db_numrows($result) < 1) {
		exit_permission_denied();
	}

	if (!$func) {
		$func='browse';
	}
	/*
		Figure out which function we're dealing with here
	*/

	switch ($func) {

		case 'addtask' : {
			if (user_ismember($group_id,'P2')) {
				include '../pm/add_task.php';
			} else {
				exit_permission_denied();
			}
			break;;
		}

		case 'postaddtask' : {
			if (user_ismember($group_id,'P2')) {
				include '../pm/postadd_task.php';
				include '../pm/browse_task.php';
			} else {
				exit_permission_denied();
			}
			break;;
		}

		case 'postmodtask' : {
			if (user_ismember($group_id,'P2')) {
				include '../pm/postmod_task.php';
				include '../pm/browse_task.php';
				break;;
			} else {
				exit_permission_denied();
			}
		}

		case 'browse' : {
			include '../pm/browse_task.php';
			break;;
		}

		case 'detailtask' : {
			if (user_ismember($group_id,'P2')) {
				include '../pm/mod_task.php';
			} else {
				include '../pm/detail_task.php';
			}
			break;;
		}

	}

} else {
	//browse for group first message
	if (!$group_id || !$group_project_id) {
		exit_no_group();
	} else {
		exit_permission_denied();
	}
}
?>
