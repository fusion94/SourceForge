<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.9 2000/06/03 13:31:03 tperdue Exp $

require('pre.php');
require('../support/support_utils.php');
require('../support/support_data.php');

if ($group_id) {

	switch ($func) {
		case 'addsupport' : {
			include '../support/add_support.php';
			break;
		}
		case 'postaddsupport' : {
			$support_id=support_data_create_support($group_id,$support_category_id,$user_email,$summary,$details);

			//send an email to the submittor and default address for the project
			mail_followup($support_id,group_get_new_support_address($group_id));
			include '../support/browse_support.php';
			break;
		}
		case 'postmodsupport' : {
			echo support_data_handle_update ($group_id,$support_id,$priority,$support_status_id,
				$support_category_id,$assigned_to,$summary,$canned_response,$details);
			if ($mail_followup) {
				mail_followup($support_id);
			}
			include '../support/browse_support.php';
			break;
		}
		case 'postaddcomment' : {
			include '../support/postadd_comment.php';
			include '../support/browse_support.php';
			break;
		}
		case 'browse' : {
			include '../support/browse_support.php';
			break;
		}
		case 'detailsupport' : {
			if (user_ismember($group_id,'S2')) {
				include '../support/mod_support.php';
			} else {
				include '../support/detail_support.php';
			}
			break;
		}
		default : {
			include '../support/browse_support.php';
			break;
		}
	}

} else {

	exit_no_group();

}

?>
