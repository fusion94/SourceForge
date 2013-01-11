<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: support_utils.php,v 1.31 2000/04/21 14:10:53 tperdue Exp $

/*

	Support Request Manager 
	By Tim Perdue, Sourceforge, January, 2000
	Heavy Rewrite Tim Perdue, April, 2000

*/

function support_header($params) {
	global $group_id,$DOCUMENT_ROOT;
	$params['group']=$group_id;
	site_header($params);

	html_tabs('support',$group_id);

	if ($group_id) {
		echo '<P><B><A HREF="/support/?func=addsupport&group_id='.$group_id.'">Submit A Support Request</A>';
		if (user_isloggedin()) {
			echo ' | <A HREF="/support/?func=browse&group_id='.$group_id.'&set=my">My Support Requests</A>';
		}
		echo ' | <A HREF="/support/?func=browse&group_id='.$group_id.'&set=closed">Closed</A>';
		echo ' | <A HREF="/support/?func=browse&group_id='.$group_id.'&set=open">Open</A>';
		echo ' | <A HREF="/support/admin/?group_id='.$group_id.'">Admin</A>';

		echo '</B>';
	}

}

function support_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function support_category_box ($group_id,$name='support_category_id',$checked='xzxz') {
	if (!$group_id) {
		return 'ERROR - No group_id';
	} else {
		$result= support_data_get_categories ($group_id);
		return util_build_select_box ($result,$name,$checked);
	}
}

function support_technician_box ($group_id,$name='assigned_to',$checked='xzxz') {
	if (!$group_id) {
		return 'ERROR - No group_id';
	} else {
		$result= support_data_get_technicians ($group_id);
		return util_build_select_box ($result,$name,$checked);
	}
}

function support_canned_response_box ($group_id,$name='canned_response',$checked='xzxz') {
	if (!$group_id) {
		return 'ERROR - No group_id';
	} else {
		$result= support_data_get_canned_responses ($group_id);
		return util_build_select_box ($result,$name,$checked);
	}
}

function support_status_box ($name='status_id',$checked='xzxz') {
	$result=support_data_get_statuses();
	return util_build_select_box($result,$name,$checked);
}

function show_supportlist ($result,$offset,$set='open') {
	global $sys_datefmt,$group_id;
	/*
		Accepts a result set from the support table. Should include all columns from
		the table, and it should be joined to USER to get the user_name.
	*/

	$url = "/support/?group_id=$group_id&set=$set&order=";
	echo '
		<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
		<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'support_id"><FONT COLOR="#FFFFFF"><B>Request ID</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'summary"><FONT COLOR="#FFFFFF"><B>Summary</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'date"><FONT COLOR="#FFFFFF"><B>Date</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'assigned_to_user"><FONT COLOR="#FFFFFF"><B>Assigned To</A></TD>
		<TD ALIGN="MIDDLE"><a class=sortbutton href="'.$url.'submitted_by"><FONT COLOR="#FFFFFF"><B>Submitted By</A></TD></TR>';

	$then=(time()-1296000);
	$rows=db_numrows($result);
	for ($i=0; $i < $rows; $i++) {
		echo '
			<TR BGCOLOR="'. get_priority_color(db_result($result, $i, 'priority')) .'">'.
			'<TD><A HREF="'.$PHP_SELF.'?func=detailsupport&support_id='. db_result($result, $i, 'support_id').
			'&group_id='. db_result($result, $i, 'group_id').'">'. db_result($result, $i, 'support_id') .'</A></TD>'.
			'<TD>'. db_result($result, $i, 'summary') .'</TD>'.
			'<TD>'. (($set != 'closed' && db_result($result, $i, 'date') < $then)?'<B>* ':'&nbsp; ') . date($sys_datefmt,db_result($result, $i, 'date')) .'</TD>'.
			'<TD>'. db_result($result, $i, 'assigned_to_user') .'</TD>'.
			'<TD>'. db_result($result, $i, 'submitted_by') .'</TD></TR>';

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

function mail_followup($support_id) {
	global $sys_datefmt,$feedback;
	/*
		Send a message to the person who opened this support and the person it is assigned to
	*/

	$sql="SELECT support.priority,support.group_id,support.support_id,support.summary,support_status.status_name,support_category.category_name, ".
		"user.email,user2.email AS assigned_to_email ".
		"FROM support,user,user user2,support_status,support_category ".
		"WHERE user2.user_id=support.assigned_to ".
		"AND support.support_status_id=support_status.support_status_id ".
		"AND support.support_category_id=support_category.support_category_id ".
		"AND user.user_id=support.submitted_by AND support.support_id='$support_id'";

	$result=db_query($sql);

	if ($result && db_numrows($result) > 0) {
		/*
			Set up the body
		*/
		$body = "\n\nSupport Request #".db_result($result,0,'support_id').", which you submitted on ".date($sys_datefmt,db_result($result,0,'open_date')). 
			"\nhas been updated. You can respond by visiting: ".
			"\nhttp://sourceforge.net/support/?func=detailsupport&support_id=".db_result($result,0,"support_id")."&group_id=".db_result($result,0,"group_id").
			"\n\nCategory: ".db_result($result,0,'category_name').
			"\nStatus: ".db_result($result,0,'status_name').
			"\nPriority: ".db_result($result,0,'priority').
			"\nSummary: ".util_unconvert_htmlspecialchars(db_result($result,0,'summary'));


		$subject="[ ".db_result($result,0,"support_id")." ] ".util_unconvert_htmlspecialchars(db_result($result,0,"summary"));

		/*
			get all the email addresses that have dealt with this request
		*/

		$email_res=db_query("SELECT distinct from_email FROM support_messages WHERE support_id='$support_id'");
		$rows=db_numrows($email_res);
		if ($email_res && $rows > 0) {
			$mail_arr=result_column_to_array($email_res,0);
			$emails=implode($mail_arr,', ');
//			for ($i=0; $i<$rows; $i++) {
//				$emails .= db_result($email_res,$i,'from_email').', ';
//			}
		}

//		$to=$emails;

		/*
			Now include the two most recent emails
		*/
		$sql="select * ".
			"FROM support_messages ".
			"WHERE support_id='$support_id' ORDER BY date DESC LIMIT 2";
		$result2=db_query($sql);
		$rows=db_numrows($result2);
		if ($result && $rows > 0) {
			for ($i=0; $i<$rows; $i++) {
				//get the first part of the email address
				$email_arr=explode('@',db_result($result2,$i,'from_email'));

				$body .= "\n\nBy: ". $email_arr[0] .
				"\nDate: ".date($sys_datefmt,db_result($result2,$i,'date')).
				"\n\nMessage:".
				"\n".util_unconvert_htmlspecialchars(db_result($result2,$i,'body')).
				"\n\n----------------------------------------------------------------------";
			}
			$body .= "\nYou can respond by visiting: ".
			"\nhttp://sourceforge.net/support/?func=detailsupport&support_id=".db_result($result,0,'support_id')."&group_id=".db_result($result,0,'group_id');
		}

		//attach the headers to the body

		$body = "To: noreply@sourceforge.net".
			"\nBCC: $emails".
			"\nSubject: $subject".
			$body;
		/*
			Send the email
		*/
		exec ("/bin/echo \"". util_prep_string_for_sendmail($body) ."\" | /usr/sbin/sendmail -fnoreply@sourceforge.net -t &");
//		echo $body;
		$feedback .= " Support Request Update Emailed ";

	} else {

		$feedback .= " Could Not Send Support Request Update ";
		echo db_error();

	}
}

function show_support_details ($support_id) {
	/*
		Show the details rows from support_history
	*/
	global $sys_datefmt;
	$result= support_data_get_messages ($support_id);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Followups</H3>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS['COLOR_MENUBARBACK'].'">
			<TD><FONT COLOR="#FFFFFF"><B>Message</TD></TR>';
		for ($i=0; $i < $rows; $i++) {
			$email_arr=explode('@',db_result($result,$i,'from_email'));
			echo '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD><PRE>
Date: '. date($sys_datefmt,db_result($result, $i, 'date')) .'
Sender: '. $email_arr[0] . '
'. db_result($result, $i, 'body'). '</PRE></TD></TR>';
		}
		echo '</TABLE>';
	} else {
		echo '
			<H3>No Followups Have Been Posted</H3>';
	}
}

function show_supporthistory ($support_id) {
	/*
		show the support_history rows that are relevant to this support_id, excluding details
	*/
	global $sys_datefmt;
	$result= support_data_get_history ($support_id);
	$rows= db_numrows($result);

	if ($rows > 0) {

		echo '
			<H3>Support Request Change History</H3>
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

			if ($field == 'support_status_id') {

				echo support_data_get_status_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'support_category_id') {

				echo support_data_get_category_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'assigned_to') {

				echo user_getname(db_result($result, $i, 'old_value'));

			} else if ($field == 'close_date') {

				echo date($sys_datefmt,db_result($result, $i, 'old_value'));

			} else {

				echo db_result($result, $i, 'old_value');

			}
			echo '</TD>'.
				'<TD>'. date($sys_datefmt,db_result($result, $i, 'date')) .'</TD>'.
				'<TD>'. db_result($result, $i, 'user_name'). '</TD></TR>';
		}

		echo '
			</TABLE>';
	
	} else {
		echo '
			<H3>No Changes Have Been Made to This Support Request</H3>';
	}
}

?>
