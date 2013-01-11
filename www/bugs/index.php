<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.24 2000/01/13 18:36:34 precision Exp $

require('pre.php');
require('../bugs/bug_utils.php');

if ($group_id) {

	switch ($func) {

		case 'addbug' : {
			include '../bugs/add_bug.php';
			break;
		}

		case 'postaddbug' : {
			include '../bugs/postadd_bug.php';
			include '../bugs/browse_bug.php';
			break;
		}

		case 'postmodbug' : {
			include '../bugs/postmod_bug.php';
			include '../bugs/browse_bug.php';
			break;
		}

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
