<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_comment.php,v 1.7 2000/04/21 13:44:46 tperdue Exp $

if (!user_isloggedin()) {
	if (!$user_email) {
		//force them to fill in user_email if they aren't logged in
		exit_error('ERROR','Go Back and fill in the user_email address or login');
	}
} else {
	//use their user_name if they are logged in
	$user_email=user_getname().'@users.sourceforge.net';
}

if ($details != '') {
	//create the first message for this ticket
	$result= support_data_create_message($details,$support_id,$user_email);
	if (!$result) {
		$feedback .= ' Comment Failed ';
	} else {
		$feedback .= ' Comment added to support request ';
		mail_followup($support_id);
	}
}

?>
