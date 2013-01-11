<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: patch_utils.php,v 1.13 2000/04/21 13:23:37 tperdue Exp $

/*

	Patch Manager 
	By Tim Perdue, Sourceforge, Feb 2000
	Heavy Rewrite Tim Perdue, April, 2000

*/

function patch_header($params) {
	global $group_id,$DOCUMENT_ROOT;
	$params['group']=$group_id;
	site_header($params);

	html_tabs('patch',$group_id);

	if ($group_id) {
		echo '<P><B><A HREF="/patch/?func=addpatch&group_id='.$group_id.'">Submit A Patch</A>';
		if (user_isloggedin()) {
			echo ' | <A HREF="/patch/?func=browse&group_id='.$group_id.'&set=my">My Patches</A>';
		}
		echo ' | <A HREF="/patch/?func=browse&group_id='.$group_id.'&set=closed">Closed</A>';
		echo ' | <A HREF="/patch/?func=browse&group_id='.$group_id.'&set=postponed">Postponed</A>';
		echo ' | <A HREF="/patch/?func=browse&group_id='.$group_id.'&set=open">Open</A>';
		echo ' | <A HREF="/patch/admin/?group_id='.$group_id.'">Admin</A>';

		echo '</B>';
	}

}

function patch_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function patch_category_box($group_id,$name='patch_category_id',$checked='xzxz') {
	if (!$group_id) {
		return 'ERROR - no group_id';
	} else {
		/*
			List of possible patch_categories set up for the project
		*/
		$sql="select patch_category_id,category_name from patch_category WHERE group_id='$group_id'";
		$result=db_query($sql);

		return util_build_select_box($result,$name,$checked);
	}
}

function patch_technician_box($group_id,$name='assigned_to',$checked='xzxz') {
	if (!$group_id) {
		return 'ERROR - no group_id';
	} else {
		$sql="SELECT user.user_id,user.user_name ".
			"FROM user,user_group ".
			"WHERE user.user_id=user_group.user_id ".
			"AND user_group.patch_flags IN (1,2) ".
			"AND user_group.group_id='$group_id'";
		$result=db_query($sql);
		return util_build_select_box($result,$name,$checked);
	}
}

function patch_status_box($name='status_id',$checked='xzxz') {
	$sql="select * from patch_status";
	$result=db_query($sql);
	return util_build_select_box($result,$name,$checked);
}

function show_patchlist ($result,$offset,$set='open') {
	global $sys_datefmt,$group_id;
	/*
		Accepts a result set from the patch table. Should include all columns from
		the table, and it should be joined to USER to get the user_name.
	*/

	$rows=db_numrows($result);
	$url = "/patch/?group_id=$group_id&set=$set&order=";
	echo '
		<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
		<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'patch_id"><FONT COLOR="#FFFFFF"><B>Patch ID</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'summary"><FONT COLOR="#FFFFFF"><B>Summary</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'date"><FONT COLOR="#FFFFFF"><B>Date</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'assigned_to_user"><FONT COLOR="#FFFFFF"><B>Assigned To</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'submitted_by"><FONT COLOR="#FFFFFF"><B>Submitted By</A></TD></TR>';

	for ($i=0; $i < $rows; $i++) {
		echo '
			<TR BGCOLOR="'. util_get_alt_row_color($i) .'">'.
			'<TD><A HREF="'.$PHP_SELF.'?func=detailpatch&patch_id='.db_result($result, $i, 'patch_id').
			'&group_id='.db_result($result, $i, 'group_id').'">'.db_result($result, $i, 'patch_id').'</A></TD>'.
			'<TD>'.db_result($result, $i, 'summary').'</TD>'.
			'<TD>'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>'.
			'<TD>'.db_result($result, $i, 'assigned_to_user').'</TD>'.
			'<TD>'.db_result($result, $i, 'submitted_by').'</TD></TR>';

	}

	/*
		Show extra rows for <-- Prev / Next -->
	*/
	echo '
		<TR><TD COLSPAN="2">';
	if ($offset > 0) {
		echo '<A HREF="'.$PHP_SELF.'?func=browse&group_id='.$group_id.'&set='.$set.'&offset='.($offset-50).'"><B><-- Previous 50</B></A>';
	} else {
		echo '&nbsp;';
	}
	echo '</TD><TD>&nbsp;</TD><TD COLSPAN="2">';
	
	if ($rows==50) {
		echo '<A HREF="'.$PHP_SELF.'?func=browse&group_id='.$group_id.'&set='.$set.'&offset='.($offset+50).'"><B>Next 50 --></B></A>';
	} else {
		echo '&nbsp;';
	}
	echo '</TD></TR></TABLE>';
}

function get_patch_status_name($string) {
	/*
		simply return status_name from patch_status
	*/
	$sql="select * from patch_status WHERE patch_status_id='$string'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'status_name');
	} else {
		return 'Error - Not Found';
	}
}

function get_patch_category_name($string) {
	/*
		simply return the category_name from patch_category
	*/
	$sql="select * from patch_category WHERE patch_category_id='$string'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'category_name');
	} else {
		return 'Error - Not Found';
	}
}

function mail_followup($patch_id) {
	global $sys_datefmt,$feedback;
	/*
		Send a message to the person who opened this patch and the person it is assigned to
	*/

	$sql="SELECT patch.group_id,patch.patch_id,patch.summary, ".
		"user.email,user2.email AS assigned_to_email ".
		"FROM patch,user,user user2 ".
		"WHERE user2.user_id=patch.assigned_to ".
		"AND user.user_id=patch.submitted_by AND patch.patch_id='$patch_id'";

	$result=db_query($sql);

	if ($result && db_numrows($result) > 0) {

		$body = "Patch #".db_result($result,0,"patch_id")." has been updated. ".
			"\nVisit SourceForge.net for more info.".
			"\n\nhttp://sourceforge.net/patch/?func=detailpatch&patch_id=".db_result($result,0,'patch_id'). '&group_id='. db_result($result,0,'group_id');

		$subject="[Patch #".db_result($result,0,'patch_id').'] '.util_unconvert_htmlspecialchars(db_result($result,0,'summary'));

		$to=db_result($result,0,'email'). ', '. db_result($result,0,'assigned_to_email');

		$more='From: noreply@sourceforge.net';

		mail($to,$subject,$body,$more);

		$feedback .= " Patch Update Sent to $to ";

	} else {

		$feedback .= " Could Not Send Patch Update ";
		echo db_error();

	}
}

function show_patch_details ($patch_id) {
	/*
		Show the details rows from patch_history
	*/
	global $sys_datefmt;
	$sql="select patch_history.field_name,patch_history.old_value,patch_history.date,user.user_name ".
		"FROM patch_history,user ".
		"WHERE patch_history.mod_by=user.user_id ".
		"AND patch_history.field_name = 'details' ".
		"AND patch_id='$patch_id' ORDER BY patch_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Followups</H3>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
			<TD><FONT COLOR="#FFFFFF"><B>Comment</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date</TD>
			<TD><FONT COLOR="#FFFFFF"><B>By</TD></TR>';
		for ($i=0; $i < $rows; $i++) {
			echo '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD>'.
				nl2br( db_result($result, $i, 'old_value') ) .'</TD>'.
				'</TD>'.
				'<TD VALIGN="TOP">'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>'.
				'<TD VALIGN="TOP">'.db_result($result, $i, 'user_name').'</TD></TR>';
		}
		echo '</TABLE>';
	} else {
		echo '
			<H3>No Followups Have Been Posted</H3>';
	}
}

function show_patchhistory ($patch_id) {
	/*
		show the patch_history rows that are relevant to this patch_id, excluding details
	*/
	global $sys_datefmt;
	$sql="select patch_history.field_name,patch_history.old_value,patch_history.date,user.user_name ".
		"FROM patch_history,user ".
		"WHERE patch_history.mod_by=user.user_id ".
		"AND patch_history.field_name <> 'details' ".
		"AND patch_id='$patch_id' ORDER BY patch_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {

		echo '
			<H3>Patch Change History</H3>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
			<TD><FONT COLOR="#FFFFFF"><B>Field</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Old Value</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date</TD>
			<TD><FONT COLOR="#FFFFFF"><B>By</TD></TR>';

		for ($i=0; $i < $rows; $i++) {
			$field=db_result($result, $i, 'field_name');
			echo '
				<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD>'.$field.'</TD><TD>';

			if ($field == 'patch_status_id') {

				echo get_patch_status_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'patch_category_id') {

				echo get_patch_category_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'assigned_to') {

				echo user_getname(db_result($result, $i, 'old_value'));

			} else if ($field == 'close_date') {

				echo date($sys_datefmt,db_result($result, $i, 'old_value'));

			} else {

				echo db_result($result, $i, 'old_value');

			}
			echo '</TD>'.
				'<TD>'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>'.
				'<TD>'.db_result($result, $i, 'user_name').'</TD></TR>';
		}

		echo '
			</TABLE>';
	
	} else {
		echo '
			<H3>No Changes Have Been Made to This Patch</H3>';
	}
}

function patch_history_create($field_name,$old_value,$patch_id) {
	/*
		handle the insertion of history for these parameters
	*/
	if (!user_isloggedin()) {
		$user=100;
	} else {
		$user=user_getid();
	}

	$sql="insert into patch_history(patch_id,field_name,old_value,mod_by,date) ".
		"VALUES ('$patch_id','$field_name','$old_value','$user','".time()."')";
	$result=db_query($sql);
	if (!$result) {
		echo "\n<H1>Error inserting history for $field_name</H1>";
		echo db_error();
	}
}

?>
