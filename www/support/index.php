<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.4 2000/04/21 13:40:26 tperdue Exp $

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
			include '../support/postadd_support.php';
			include '../support/browse_support.php';
			break;
		}
		case 'postmodsupport' : {
			include '../support/postmod_support.php';
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
