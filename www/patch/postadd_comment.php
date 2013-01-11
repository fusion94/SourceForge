<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_comment.php,v 1.6 2000/01/26 16:25:37 tperdue Exp $

if ($details != '') { 
	patch_history_create('details',addslashes(htmlspecialchars($details)),$patch_id);  
	$feedback .= ' Comment added to patch ';
	mail_followup($patch_id);
}

//user is uploading a new version of the patch
if ($upload_new && user_isloggedin()) {

	//see if this user submitted this patch
	$result=db_query("SELECT * FROM patch WHERE submitted_by='".user_getid()." AND patch_id='$patch_id'");
	if (!$result || db_numrows($result) < 1) {
		patch_header(array ('title'=>'Patch Modification Failed'));
                echo '
                        <H1>Error - Permission problem or patch not found!</H1>
			<P>
			<B>Only the original submittor of a patch can upload a new version.</B>';
                echo db_error();
                patch_footer(array());
                exit;
	} else {
		//patch for this user was found, so update it now

		$code = addslashes(fread( fopen($uploaded_data, 'r'), filesize($uploaded_data)));
		if ((strlen($code) > 20) && (strlen($code) < 512000)) {
			//new patch must be > 20 bytes

			$result=db_query("UPDATE patch SET code='".htmlspecialchars($code)."' WHERE submitted_by='".user_getid()." AND patch_id='$patch_id'");

			//see if the update actually worked
			if (!$result || db_affected_rows($result) < 1) {
				$feedback .= ' Patch not changed - error ';
				echo db_error();
			} else {
				patch_history_create('Patch Code','Modified - New Version',$patch_id);
				$feedback .= ' Patch Code Updated ';
			}
		} else {
			$feedback .= ' Patch not changed - patch must be > 20 chars and < 512000 chars in length ';
		}
	}
} else if ($upload_new) {
	$feedback .= ' Patch not changed - you must be logged in ';
}


?>
