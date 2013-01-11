<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postmod_patch.php,v 1.12 2000/04/20 14:33:33 tperdue Exp $

$sql="SELECT * FROM patch WHERE patch_id='$patch_id'";

$result=db_query($sql);

if ((db_numrows($result) > 0) && (user_ismember(db_result($result,0,'group_id'),'C2'))) {

	//user is uploading a new version of the patch

	if ($upload_new) {
        	$code = addslashes(fread( fopen($uploaded_data, 'r'), filesize($uploaded_data)));
		if ((strlen($code) > 20) && (strlen($code) < 512000)) {
			$codesql=", code='".htmlspecialchars($code)."' ";
			 patch_history_create('Patch Code','Modified - New Version',$patch_id);
		} else {
			$feedback .= ' Patch not changed - patch must be > 20 chars and < 512000 chars in length ';
			$codesql='';
		}
	} else {
		$codesql='';
	}

	/*
		See which fields changed during the modification
	*/
	if (db_result($result,0,'patch_status_id') != $patch_status_id) { patch_history_create('patch_status_id',db_result($result,0,'patch_status_id'),$patch_id);  }
	if (db_result($result,0,'patch_category_id') != $patch_category_id) { patch_history_create('patch_category_id',db_result($result,0,'patch_category_id'),$patch_id);  }
	if (db_result($result,0,'assigned_to') != $assigned_to) { patch_history_create('assigned_to',db_result($result,0,'assigned_to'),$patch_id);  }
	if (db_result($result,0,'summary') != stripslashes(htmlspecialchars($summary))) 
		{ patch_history_create('summary',htmlspecialchars(addslashes(db_result($result,0,'summary'))),$patch_id);  }

	/*
		Details field is handled a little differently
	*/
	if ($details != '') { patch_history_create('details',htmlspecialchars($details),$patch_id);  }

	/*
		Enter the timestamp if we are changing to closed
	*/
	if ($patch_status_id == "2" || $patch_status_id == "4") {
		$now=time();
		$close_date=", close_date='$now' ";
		patch_history_create('close_date',db_result($result,0,'close_date'),$patch_id);
	} else {
		$close_date='';
	}

	/*
		Finally, update the patch itself
	*/
	$sql="UPDATE patch SET patch_status_id='$patch_status_id'$close_date $codesql, patch_category_id='$patch_category_id', ".
		"assigned_to='$assigned_to', summary='".htmlspecialchars($summary)."' ".
		"WHERE patch_id='$patch_id'";

	$result=db_query($sql);

	if (!$result) {
		patch_header(array ('title'=>'Patch Modification Failed'));
		echo '
			<H1>Error - update failed!</H1>';
		echo db_error();
		echo $sql;
		patch_footer(array());
		exit;
	} else {
		$feedback .= " Successfully Modified Patch ";
	}

	if ($mail_followup) {
		mail_followup($patch_id);
	}

} else {

	exit_permission_denied();

}

?>
