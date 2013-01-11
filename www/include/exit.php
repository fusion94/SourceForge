<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: exit.php,v 1.9 2000/01/13 18:36:35 precision Exp $

function exit_error($title,$text) {
	site_header(array('title'=>'Exiting with Error'));
	print '<H2><font color="#FF3333">'.$title.'</font></H2><P>'.$text;
	site_footer(array());
	site_cleanup(array());
	exit;
}

function exit_permission_denied() {
	exit_error('Permission Denied','This project\'s administrator will have to grant you permission to view this page.');
}

function exit_not_logged_in() {
	exit_error('Not Logged In','Sorry, you have to be <A HREF="https://'.getenv('HTTP_HOST').'/account/login.php">logged in</A> to view this page.');
}

function exit_no_group() {
	exit_error('Error - Choose a Group','ERROR - No group_id was chosen.');
}
?>
