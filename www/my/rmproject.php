<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: rmproject.php,v 1.8 2000/10/11 23:42:41 tperdue Exp $

/*

	Thanks to Wallace Lee for submitting this

	Completely rewritten by Tim Perdue 10/11/2000

*/

require ('pre.php');

if (user_isloggedin()) {
	$user_id = user_getid();

	$group=group_get_object($group_id);
	if ($group) {
		if (!$group->removeUser($user_id)) {
			exit_error('ERROR',$group->getErrorMessage());
		} else {
			session_redirect("/my/");
		}
	} else {
		exit_no_group();
	}

} else {

	exit_not_logged_in();

}

?>
