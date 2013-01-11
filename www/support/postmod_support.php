<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postmod_support.php,v 1.11 2000/04/21 13:23:37 tperdue Exp $

$sql="SELECT * FROM support WHERE support_id='$support_id'";

$result=db_query($sql);

if ((db_numrows($result) > 0) && (user_ismember(db_result($result,0,'group_id'),'S2'))) {

	/*
		See which fields changed during the modification
	*/
	if (db_result($result,0,'priority') != $priority) 
		{ support_data_create_history('priority',db_result($result,0,'priority'),$support_id);  }
	if (db_result($result,0,'support_status_id') != $support_status_id) 
		{ support_data_create_history('support_status_id',db_result($result,0,'support_status_id'),$support_id);  }
	if (db_result($result,0,'support_category_id') != $support_category_id) 
		{ support_data_create_history('support_category_id',db_result($result,0,'support_category_id'),$support_id);  }
	if (db_result($result,0,'assigned_to') != $assigned_to) 
		{ support_data_create_history('assigned_to',db_result($result,0,'assigned_to'),$support_id);  }
	if (db_result($result,0,'summary') != stripslashes(htmlspecialchars($summary))) 
		{ support_data_create_history('summary',htmlspecialchars(addslashes(db_result($result,0,'summary'))),$support_id);  }

	/*
		handle canned responses
	*/
	if ($canned_response != 100) {
		//don't care if this response is for this group - could be hacked
		$sql="SELECT * FROM support_canned_responses WHERE support_canned_id='$canned_response'";
		$result2=db_query($sql);
		if ($result2 && db_numrows($result2) > 0) {
			support_data_create_message(util_unconvert_htmlspecialchars(db_result($result2,0,'body')),$support_id,user_getname().'@users.sourceforge.net');
			$feedback .= ' Canned Response Used ';
		} else {
			$feedback .= ' Unable to Use Canned Response ';
		}
	}

	/*
		Details field is handled a little differently
	*/
	if ($details != '') {
		//create the first message for this ticket
		support_data_create_message($details,$support_id,user_getname().'@users.sourceforge.net');
		$feedback .= ' Comment added to support request ';
		//mail_followup($support_id);
	}

	/*
		Enter the timestamp if we are changing to closed
	*/
	if ($support_status_id == "2") {
		$now=time();
		$close_date=", close_date='$now' ";
		support_data_create_history('close_date',db_result($result,0,'close_date'),$support_id);
	} else {
		$close_date='';
	}

	/*
		Finally, update the support request itself
	*/
	$sql="UPDATE support SET support_status_id='$support_status_id'$close_date, support_category_id='$support_category_id', ".
		"assigned_to='$assigned_to', priority='$priority', summary='".htmlspecialchars($summary)."' ".
		"WHERE support_id='$support_id'";

	$result=db_query($sql);

	if (!$result) {
		exit_error('Error','UPDATE FAILED '.db_error());
	} else {
		$feedback .= " Successfully Modified Support Request ";
	}

	if ($mail_followup) {
		mail_followup($support_id);
	}

} else {

	exit_permission_denied();

}

?>
