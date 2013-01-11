<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: bug_utils.php,v 1.123 2000/01/13 18:36:34 precision Exp $

function bug_header($params) {
	global $group_id,$is_bug_page,$DOCUMENT_ROOT;
	$is_bug_page=1;
	$params['group']=$group_id;
	site_header($params);

	echo "&nbsp;<BR>";

	html_tabs('bugs',$group_id);

	if ($group_id) {
		echo '<P><B><A HREF="/bugs/?func=addbug&group_id='.$group_id.'">Submit</A>
		 | <A HREF="/bugs/?func=browse&group_id='.$group_id.'&set=open">Open Bugs</A>';
		if (user_isloggedin()) {
			echo ' | <A HREF="/bugs/?func=browse&group_id='.$group_id.'&set=my">My Bugs</A>';
		}
		echo ' | <A HREF="/bugs/?func=browse&group_id='.$group_id.'&set=closed">Closed Bugs</A>';
		if (user_isloggedin()) {
			echo ' | <A HREF="/bugs/?func=modfilters&group_id='.$group_id.'">Filters</A>';
		}
		echo ' | <A HREF="/bugs/reporting/?group_id='.$group_id.'">Reporting</A>
		 | <A HREF="/bugs/admin/?group_id='.$group_id.'">Admin</A></B>';
	}

}

function bug_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function show_filters ($group_id) {
	/*
		The goal here is to show any existing bug filters for this user/group combo.
		In addition, we are going to show an empty row where a new filter can be created
	*/
	$sql="SELECT * FROM bug_filter WHERE user_id='".user_getid()."' AND group_id='$group_id'";
	$result=db_query($sql);

	echo '<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="2">';

	if ($result && db_numrows($result) > 0) {
		for ($i=0; $i<db_numrows($result); $i++) {
			if ($i % 2 == 0) {
				$row_color=' BGCOLOR="#FFFFFF"';
			} else {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}
			/*
				iterate and show the existing filters
			*/
			?>
			<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
			<INPUT TYPE="HIDDEN" NAME="func" VALUE="postmodfilters">
			<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
			<INPUT TYPE="HIDDEN" NAME="subfunc" VALUE="mod">
			<INPUT TYPE="HIDDEN" NAME="filter_id" VALUE="<?php 
				echo db_result($result,$i,"filter_id"); 
			?>">
			<TR<?php echo $row_color; ?>>
				<TD><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="delete_filter" VALUE="Delete"><BR><INPUT TYPE="SUBMIT" NAME="submit" VALUE="Modify/Activate"></TD>
				<TD NOWRAP><FONT SIZE="-1">SELECT * FROM bug WHERE<BR>bug.group_id='<?php echo $group_id; ?>' AND (</TD>
				<TD NOWRAP><FONT SIZE="-1"><INPUT TYPE="TEXT" SIZE="60" MAXLENGTH="250" NAME="sql_clause" VALUE="<?php 
						echo stripslashes(db_result($result,$i,"sql_clause")); 
					?>"></TD>
				<TD NOWRAP><FONT SIZE="-1">) LIMIT 0,50</TD>
			</TR></FORM>
			<?php

		}
	}

	/*
		empty form for new filter
	*/
	if ($i % 2 == 0) {
	       $row_color=' BGCOLOR="#FFFFFF"';
	} else {
	       $row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
	}

	?>
	<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="func" VALUE="postmodfilters">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
	<INPUT TYPE="HIDDEN" NAME="subfunc" VALUE="add">
	<TR<?php echo $row_color; ?>>
		<TD><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Add"></TD>
		<TD NOWRAP><FONT SIZE="-1">SELECT * FROM bug WHERE<BR>bug.group_id='<?php echo $group_id; ?>' AND (</TD>
		<TD NOWRAP><FONT SIZE="-1"><INPUT TYPE="TEXT" SIZE="60" MAXLENGTH="250" NAME="sql_clause" VALUE="bug.status_id IN (1,2,3) OR bug.priority > 0 OR bug.bug_group_id IN (1,2,3,4) OR bug.resolution_id IN (1,2,3) OR bug.assigned_to IN (1,2,3,4,5,6) OR bug.category_id IN (1,2,3)"></TD>
		<TD NOWRAP><FONT SIZE="-1">) LIMIT 0,50</TD>
	</TR></FORM>
	</TABLE>
	<P>
	<FORM ACTION="<?php echo $PHP_SELF; ?>" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="func" VALUE="postmodfilters">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
	<INPUT TYPE="HIDDEN" NAME="subfunc" VALUE="turn_off">
	<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Deactivate Filters">
	</FORM>
<?php

}

function show_buglist ($result,$offset,$set='open') {
	global $sys_datefmt,$group_id;
	/*
		Accepts a result set from the bugs table. Should include all columns from
		the table, and it should be joined to USER to get the user_name.
	*/

	$rows=db_numrows($result);
	echo '
		<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
		<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'"><TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Bug ID</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Summary</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Date</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Assigned To</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Submitted By</TD></TR>';

	for ($i=0; $i < $rows; $i++) {

		echo '
			<TR BGCOLOR="'.get_priority_color(db_result($result, $i, 'priority')).'">'.
			'<TD><A HREF="'.$PHP_SELF.'?func=detailbug&bug_id='.db_result($result, $i, 'bug_id').
			'&group_id='.db_result($result, $i, 'group_id').'">'.db_result($result, $i, 'bug_id').'</A></TD>'.
			'<TD>'.stripslashes(db_result($result, $i, 'summary')).'</TD>'.
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

function get_bug_status_name($string) {
	/*
		simply return status_name from bug_status
	*/
	$sql="select * from bug_status WHERE status_id='$string'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'status_name');
	} else {
		return 'Error - Not Found';
	}
}

function mail_followup($bug_id) {
	global $sys_datefmt,$feedback;
	/*
		Send a message to the person who opened this bug and the person it is assigned to
	*/

	$sql="SELECT bug.date,bug.details,bug.group_id,bug.priority,bug.bug_id,bug.summary,bug_resolution.resolution_name,bug_group.group_name,".
		"bug.date,bug_category.category_name,bug_status.status_name,user.user_name,user.email,user2.email AS assigned_to_email ".
		"FROM bug,user,user user2,bug_category,bug_status,bug_group,bug_resolution ".
		"WHERE user2.user_id=bug.assigned_to AND bug.status_id=bug_status.status_id ".
		"AND bug_resolution.resolution_id=bug.resolution_id AND bug_group.bug_group_id=bug.bug_group_id ".
		"AND bug.category_id=bug_category.bug_category_id AND user.user_id=bug.submitted_by AND bug.bug_id='$bug_id'";

	$result=db_query($sql);

	if ($result && db_numrows($result) > 0) {

		$body = "Bug #".db_result($result,0,"bug_id").", which you submitted on ".date($sys_datefmt,db_result($result,0,"date"))." has ".
			"\nbeen updated. Here is a current snapshot of the bug.".
			"\n\nCategory: ".db_result($result,0,"category_name").
			"\nStatus: ".db_result($result,0,"status_name").
			"\nResolution: ".db_result($result,0,"resolution_name").
			"\nBug Group: ".db_result($result,0,"group_name").
			"\nSummary: ".util_unconvert_htmlspecialchars(stripslashes(stripslashes(db_result($result,0,"summary")))).
			"\n\nDetails: ".util_unconvert_htmlspecialchars(stripslashes(stripslashes(db_result($result,0,"details"))));

		$sql="SELECT user.email,user.user_name,bug_history.date,bug_history.old_value FROM bug_history,user WHERE user.user_id=bug_history.mod_by AND bug_history.field_name='details' AND bug_history.bug_id='$bug_id'";
		$result2=db_query($sql);
		$rows=db_numrows($result2);
		if ($result2 && $rows > 0) {
			$body .= "\n\nFollow-Ups:";
			for ($i=0; $i<$rows;$i++) {
				$body .= "\n\nDate: ".date($sys_datefmt,db_result($result2,$i,"date"));
				$body .= "\nBy: ".db_result($result2,$i,"user_name");
				$body .= "\n\nComment:\n".util_unconvert_htmlspecialchars(stripslashes(stripslashes(db_result($result2,$i,"old_value"))));
				$body .= "\n-------------------------------------------------------";
			}
		}
		$body .= "\n\nIf you have questions, please visit SourceForge.";

		$subject="[Bug #".db_result($result,0,"bug_id")."] ".util_unconvert_htmlspecialchars(stripslashes(stripslashes(db_result($result,0,"summary"))));

		$to=db_result($result,0,"email").", ".db_result($result,0,"assigned_to_email");

		$more="From: noreply@sourceforge.net";

		mail($to,$subject,$body,$more);

		$feedback .= " Bug Update Sent to $to ";

	} else {

		$feedback .= " Could Not Send Bug Update ";

	}
}

function get_bug_category_name($string) {
	/*
		simply return the category_name from bug_category
	*/
	$sql="select * from bug_category WHERE bug_category_id='$string'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'category_name');
	} else {
		return 'Error - Not Found';
	}
}

function get_bug_resolution_name($resolution_id) {
	/*
		Simply return the resolution name for this id
	*/

	$sql="select * from bug_resolution WHERE resolution_id='$resolution_id'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'resolution_name');
	} else {
		return 'Error - Not Found';
	}
}

function get_bug_group_name($bug_group_id) {
	/*
		Simply return the resolution name for this id
	*/

	$sql="select * from bug_group WHERE bug_group_id='$bug_group_id'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'group_name');
	} else {
		return 'Error - Not Found';
	}
}

function show_dependent_bugs ($bug_id,$group_id) {
	$sql="SELECT bug.bug_id,bug.summary ".
		"FROM bug,bug_bug_dependencies ".
		"WHERE bug.bug_id=bug_bug_dependencies.bug_id ".
		"AND bug_bug_dependencies.is_dependent_on_bug_id='$bug_id'";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Other Bugs That Depend on This Bug</H3>';
		echo '
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
				<TD><FONT COLOR="#FFFFFF"><B>Bug ID</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Summary</TD></TR>';

		for ($i=0; $i < $rows; $i++) {
			if ($i % 2 == 0) {
				$row_color = ' BGCOLOR="#FFFFFF"';
			} else {
				$row_color = ' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}

			echo '
			<TR'.$row_color.'>
				<TD><A HREF="/bugs/?func=detailbug&bug_id='.
				db_result($result, $i, 'bug_id').
				'&group_id='.$group_id.'">'.db_result($result, $i, 'bug_id').'</A></TD>
				<TD>'.db_result($result, $i, 'summary').'</TD></TR>';
		}
		echo '</TABLE>';
	} else {
		echo '
			<H3>No Other Bugs are Dependent on This Bug</H3>';
		echo db_error();
	}
}

function show_bug_details ($bug_id) {
	/*
		Show the details rows from bug_history
	*/
	global $sys_datefmt;
	$sql="select bug_history.field_name,bug_history.old_value,bug_history.date,user.user_name ".
		"FROM bug_history,user where bug_history.mod_by=user.user_id AND bug_history.field_name = 'details' AND bug_id='$bug_id' ORDER BY bug_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Followups</H3>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'"><TD><FONT COLOR="#FFFFFF"><B>Comment</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date</TD>
			<TD><FONT COLOR="#FFFFFF"><B>By</TD></TR>';
		for ($i=0; $i < $rows; $i++) {
			if ($i % 2 == 0) {
				$row_color = ' BGCOLOR="#FFFFFF"';
			} else {
				$row_color = ' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}
			echo '<TR'.$row_color.'><TD>'.
				ereg_replace("\n","<BR>",stripslashes(stripslashes(db_result($result, $i, 'old_value')))).'</TD>'.
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

function show_bughistory ($bug_id) {
	/*
		show the bug_history rows that are relevant to this bug_id, excluding details
	*/
	global $sys_datefmt;
	$sql="select bug_history.field_name,bug_history.old_value,bug_history.date,user.user_name ".
		"FROM bug_history,user where bug_history.mod_by=user.user_id AND bug_history.field_name <> 'details' AND bug_id='$bug_id' ORDER BY bug_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {

		echo '
			<H3>Bug Change History</H3>
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'"><TD><FONT COLOR="#FFFFFF"><B>Field</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Old Value</TD>
			<TD><FONT COLOR="#FFFFFF"><B>Date</TD>
			<TD><FONT COLOR="#FFFFFF"><B>By</TD></TR>';

		for ($i=0; $i < $rows; $i++) {
			$field=db_result($result, $i, 'field_name');
			if ($i % 2 == 0) {
				$row_color = ' BGCOLOR="#FFFFFF"';
			} else {
				$row_color = ' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}
			echo '
				<TR'.$row_color.'><TD>'.$field.'</TD><TD>';

			if ($field == 'status_id') {

				echo get_bug_status_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'category_id') {

				echo get_bug_category_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'assigned_to') {

				echo user_getname(db_result($result, $i, 'old_value'));

			} else if ($field == 'close_date') {

				echo date($sys_datefmt,db_result($result, $i, 'old_value'));

			} else if ($field == 'resolution_id') {

				echo get_bug_resolution_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'bug_group_id') {

				echo get_bug_group_name(db_result($result, $i, 'old_value'));

			} else {

				echo stripslashes(stripslashes(db_result($result, $i, 'old_value')));

			}
			echo '</TD>'.
				'<TD>'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>'.
				'<TD>'.db_result($result, $i, 'user_name').'</TD></TR>';
		}

		echo '
			</TABLE>';
	
	} else {
		echo '
			<H3>No Changes Have Been Made to This Bug</H3>';
	}
}

function bug_history_create($field_name,$old_value,$bug_id) {
	/*
		handle the insertion of history for these parameters
	*/
	if (!user_isloggedin()) {
        	$user=100;
	} else {
        	$user=user_getid();
	}

	$sql="insert into bug_history(bug_id,field_name,old_value,mod_by,date) VALUES ('$bug_id','$field_name','$old_value','$user','".time()."')";
	$result=db_query($sql);
	if (!$result) {
		echo "\n<H1>Error inserting history for $field_name</H1>";
		echo db_error();
	}
}

?>
