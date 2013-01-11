<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: group.php,v 1.53 2000/06/04 04:48:25 tperdue Exp $

// ############################# function group_getname()

$GROUP_RES=array();

function group_getname ($group_id = 0) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return 'ERROR - NOT FOUND';
	} else {
		return (db_result($result,0,'group_name'));
	}
}

function group_getunixname ($group_id) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return 'ERROR - NOT FOUND';
	} else {
		return (db_result($result,0,'unix_group_name'));
	}
}

function group_get_type_id ($group_id) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return 'ERROR - NOT FOUND';
	} else {
		return (db_result($result,0,'type'));
	}
}

function group_get_new_bug_address ($group_id) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return false;
	} else {
		return (db_result($result,0,'new_bug_address'));
	}
}

function group_get_new_support_address ($group_id) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return false;
	} else {
		return (db_result($result,0,'new_support_address'));
	}
}

function group_get_new_patch_address ($group_id) {
	$result=group_get_result($group_id);
	if (!$result || db_numrows($result) < 1) {
		return false;
	} else {
		return (db_result($result,0,'new_patch_address'));
	}
}

function group_get_result($group_id=0) {
	//create a common set of group result sets, 
	//so it doesn't have to be fetched each time

	global $GROUP_RES;
	if (!$GROUP_RES["_".$group_id."_"]) {
		$GROUP_RES["_".$group_id."_"]=db_query("SELECT * FROM groups WHERE group_id='$group_id'");
		return $GROUP_RES["_".$group_id."_"];
	} else {
		return $GROUP_RES["_".$group_id."_"];
	}
}

function group_get_result_by_unix($group_name=0) {
	//create a common set of group result sets,
	//so it doesn't have to be fetched each time

	global $GROUP_RES;
	$result=db_query("SELECT * FROM groups WHERE unix_group_name='$group_name'");
	$group_id=db_result($result,0,'group_id');
	//echo '-'.$group_id.'-';
	$GROUP_RES["_".$group_id."_"]=$result;
	return $GROUP_RES["_".$group_id."_"];
}


?>
