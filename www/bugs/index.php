<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.31 2000/06/20 13:26:11 tperdue Exp $

require('pre.php');
require('../bugs/bug_utils.php');
require('../bugs/bug_data.php');

if ($group_id) {

	switch ($func) {

		case 'addbug' : {
			include '../bugs/add_bug.php';
			break;
		}

		case 'postaddbug' : {
			//data control layer
			$bug_id=bug_data_create_bug($group_id,$summary,$details,$category_id,$bug_group_id);

			// send an email to notify the user and 
			// let the project know the bug was submitted
			mail_followup($bug_id, group_get_new_bug_address($group_id));
			include '../bugs/browse_bug.php';
			break;
		}

		case 'postmodbug' : {
			//data control layer
			bug_data_handle_update ($group_id,$bug_id,$status_id,$priority,$category_id,
				$assigned_to,$summary,$bug_group_id,$resolution_id,$details,
				$dependent_on_task,$dependent_on_bug,$canned_response);
			if ($mail_followup) {
				mail_followup($bug_id);
			}
			include '../bugs/browse_bug.php';
			break;
		}
/*
		case 'massupdate' : {
			//data control layer
			bug_data_mass_update ($group_id,$bug_id,$status_id,$priority,$category_id,
				$assigned_to,$bug_group_id,$resolution_id);
			include '../bugs/browse_bug.php';
			break;
		}
*/
		case 'postaddcomment' : {
			include '../bugs/postadd_comment.php';
			include '../bugs/browse_bug.php';
			break;
		}

		case 'browse' : {
			include '../bugs/browse_bug.php';
			break;
		}

		case 'detailbug' : {
			if (user_ismember($group_id,'B2')) {
				include '../bugs/mod_bug.php';
			} else {
				include '../bugs/detail_bug.php';
			}
			break;
		}

		case 'modfilters' : {
			if (user_isloggedin()) {
				include '../bugs/mod_filters.php';
				break;
			} else {
				exit_not_logged_in();
			}
		}

		case 'postmodfilters' : {
			if (user_isloggedin()) {
				include '../bugs/postmod_filters.php';
				include '../bugs/mod_filters.php';
				break;
			} else {
				exit_not_logged_in();
			}
		}

		default : {
			include '../bugs/browse_bug.php';
			break;
		}

	}

} else {

	exit_no_group();

}
?>
