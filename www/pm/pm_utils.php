<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: pm_utils.php,v 1.39 2000/01/13 18:36:36 precision Exp $

function pm_header($params) {
	global $group_id,$is_pm_page,$words,$group_project_id,$DOCUMENT_ROOT;
	$params['group']=$group_id;
	site_header($params);
	include ($DOCUMENT_ROOT.'/pm/pm_nav.php');
}

function pm_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

function ShowPercentCompleteBox($name='percent_complete',$selected=0) {
	echo '
		<select name="'.$name.'">';
	echo '
		<option value="0">Not Started';
	for ($i=5; $i<101; $i+=5) {
		echo '
			<option value="'.$i.'"';
		if ($i==$selected) {
			echo ' SELECTED';
		}	
		echo '>'.$i.'%';
	}
	echo '
		</select>';
}

function ShowMonthListSelectBox($name,$select_month=0) {

	echo '
		<select name="'.$name.'" size="1">';
	$monthlist = array('1'=>'January',
			'2'=>'February',
			'3'=>'March',
			'4'=>'April',
			'5'=>'May',
			'6'=>'June',
			'7'=>'July',
			'8'=>'August',
			'9'=>'September',
			'10'=>'October',
			'11'=>'November',
			'12'=>'December');

	for ($i=1; $i<=count($monthlist); $i++) {
		if ($i == $select_month) {
			echo '
				<option selected value="'.$i.'">'.$monthlist[$i];
		} else {
			echo '
				<option value="'.$i.'">'.$monthlist[$i];
		}
	}
	echo '
		</SELECT>';

}

function ShowDaySelectBox($name,$day=1) {

	echo '
		<select name="'.$name.'" size="1">';
	for ($i=1; $i<=31; $i++) {
		if ($i == $day) {
			echo '
				<option selected value="'.$i.'">'.$i;
		} else {
			echo '
				<option value="'.$i.'">'.$i;
		}
	}
	echo '
		</select>';

}

function ShowYearSelectBox($name,$year=1) {

	echo '
		<select name="'.$name.'" size="1">';
	for ($i=1999; $i<=2013; $i++) {
		if ($i == $year) {
			echo '
				<option selected value="'.$i.'">'.$i;
		} else {
			echo '
				<option value="'.$i.'">'.$i;
		}
	}
	echo '
		</select>';

}

function show_tasklist ($result,$offset,$set='open') {
	global $sys_datefmt,$group_id,$group_project_id,$PHP_SELF;
	/*
		Accepts a result set from the bugs table. Should include all columns from
		the table, and it should be joined to USER to get the user_name.
	*/

	$rows=db_numrows($result);
	echo '
		<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">';
	echo '
		<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Task ID</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Summary</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Start Date</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>End Date</TD>
		<TD ALIGN="MIDDLE"><FONT COLOR="#FFFFFF"><B>Percent Complete</TD></TR>';

	for ($i=0; $i < $rows; $i++) {

		echo '
			<TR BGCOLOR="'.get_priority_color(db_result($result, $i, 'priority')).'">'.
			'<TD><A HREF="'.$PHP_SELF.'?func=detailtask'.
			'&project_task_id='.db_result($result, $i, 'project_task_id').
			'&group_id='.$group_id.
			'&group_project_id='.db_result($result, $i, 'group_project_id').'">'.
			db_result($result, $i, 'project_task_id').'</A></TD>'.
			'<TD>'.stripslashes(db_result($result, $i, 'summary')).'</TD>'.
			'<TD>'.date('Y-m-d',db_result($result, $i, 'start_date')).'</TD>'.
			'<TD>'.date('Y-m-d',db_result($result, $i, 'end_date')).'</TD>'.
			'<TD>'.db_result($result, $i, 'percent_complete').'%</TD></TR>';

	}

	/*
		Show extra rows for <-- Prev / Next -->
	*/
	echo '<TR><TD COLSPAN="2">';
	if ($offset > 0) {
		echo '<A HREF="'.$PHP_SELF.'?func=browse&group_project_id='.
			$group_project_id.'&set='.$set.'&group_id='.$group_id.'&offset='.($offset-50).'">
			<B><-- Previous 50</B></A>';
	} else {
		echo '&nbsp;';
	}
	echo '</TD><TD>&nbsp;</TD><TD COLSPAN="2">';
	
	if ($rows==50) {
		echo '<A HREF="'.$PHP_SELF.'?func=browse&group_project_id='.
			$group_project_id.'&set='.$set.'&group_id='.$group_id.'&offset='.($offset+50).
			'"><B>Next 50 --></B></A>';
	} else {
		echo '&nbsp;';
	}
	echo '</TD></TR></TABLE>';
}

function get_task_status_name($string) {
	/*
		simply return status_name from bug_status
	*/
	$sql="SELECT * FROM project_status WHERE status_id='$string'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'status_name');
	} else {
		return 'Error - Not Found';
	}
}

function get_task_group_name($group_project_id) {
	/*
		Simply return the resolution name for this id
	*/

	$sql="SELECT * FROM project_group_list WHERE group_project_id='$group_project_id'";
	$result=db_query($sql);
	if ($result && db_numrows($result) > 0) {
		return db_result($result,0,'project_name');
	} else {
		return 'Error - Not Found';
	}
}

function show_dependent_tasks ($project_task_id,$group_id,$group_project_id) {
	$sql="SELECT project_task.project_task_id,project_task.summary ".
		"FROM project_task,project_dependencies ".
		"WHERE project_task.project_task_id=project_dependencies.project_task_id ".
		"AND project_dependencies.is_dependent_on_task_id='$project_task_id'";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Tasks That Depend on This Task</H3>';
		echo '
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
				<TD><FONT COLOR="#FFFFFF"><B>Task ID</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Summary</TD></TR>';

		for ($i=0; $i < $rows; $i++) {
			if ($i % 2 == 0) {
				$row_color = ' BGCOLOR="#FFFFFF"';
			} else {
				$row_color = ' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}

			echo '
			<TR'.$row_color.'>
				<TD><A HREF="/pm/task.php?func=detailtask&project_task_id='.
				db_result($result, $i, 'project_task_id').
				'&group_id='.$group_id.
				'&group_project_id='.$group_project_id.'">'.
				db_result($result, $i, 'project_task_id').'</TD>
				<TD>'.db_result($result, $i, 'summary').'</TD></TR>';
		}
		echo '</TABLE>';
	} else {
		echo '
			<H3>No Tasks are Dependent on This Task</H3>';
		echo db_error();
	}
}

function show_dependent_bugs ($project_task_id,$group_id,$group_project_id) {
	$sql="SELECT bug.bug_id,bug.summary ".
		"FROM bug,bug_task_dependencies ".
		"WHERE bug.bug_id=bug_task_dependencies.bug_id ".
		"AND bug_task_dependencies.is_dependent_on_task_id='$project_task_id'";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Bugs That Depend on This Task</H3>';
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
			<H3>No Bugs are Dependent on This Task</H3>';
		echo db_error();
	}
}


function show_task_details ($project_task_id) {
	/*
		Show the details rows from task_history
	*/
	global $sys_datefmt;
	$sql="SELECT project_history.field_name,project_history.old_value,project_history.date,user.user_name ".
		"FROM project_history,user ".
		"WHERE project_history.mod_by=user.user_id AND project_history.field_name = 'details' ".
		"AND project_task_id='$project_task_id' ORDER BY project_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		echo '
			<H3>Followups</H3>';
		echo '
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
				<TD><FONT COLOR="#FFFFFF"><B>Comment</TD>
				<TD><FONT COLOR="#FFFFFF"><B>Date</TD>
				<TD><FONT COLOR="#FFFFFF"><B>By</TD></TR>';

		for ($i=0; $i < $rows; $i++) {
			if ($i % 2 == 0) {
				$row_color = ' BGCOLOR="#FFFFFF"';
			} else {
				$row_color = ' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			}

			echo '
			<TR'.$row_color.'>
				<TD>'. nl2br(stripslashes(stripslashes(db_result($result, $i, 'old_value')))).'</TD>
				<TD VALIGN="TOP">'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>
				<TD VALIGN="TOP">'.db_result($result, $i, 'user_name').'</TD></TR>';
		}
		echo '</TABLE>';
	} else {
		echo '
			<H3>No Comments Have Been Added</H3>';
	}
	
}

function show_task_history ($project_task_id) {
	/*
		show the project_history rows that are 
		relevant to this project_task_id, excluding details
	*/
	global $sys_datefmt;
	$sql="select project_history.field_name,project_history.old_value,project_history.date,user.user_name ".
		"FROM project_history,user ".
		"WHERE project_history.mod_by=user.user_id AND ".
		"project_history.field_name <> 'details' AND project_task_id='$project_task_id' ORDER BY project_history.date DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {

		echo '
			<H3>Task Change History</H3>';
		echo '
			<TABLE WIDTH="100%" BORDER="0" CELLSPACING="1" CELLPADDING="2">
			<TR BGCOLOR="'.$GLOBALS[COLOR_MENUBARBACK].'">
			<TD><FONT COLOR="#FFFFFF"><B>Field</TD>
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

				echo get_task_status_name(db_result($result, $i, 'old_value'));

			} else if ($field == 'start_date') {

				echo date('Y-m-d',db_result($result, $i, 'old_value'));

			} else if ($field == 'end_date') {

				echo date('Y-m-d',db_result($result, $i, 'old_value'));

			} else {

				echo stripslashes(stripslashes(db_result($result, $i, 'old_value')));

			}
			echo '</TD>
				<TD>'.date($sys_datefmt,db_result($result, $i, 'date')).'</TD>
				<TD>'.db_result($result, $i, 'user_name').'</TD></TR>';
		}

		echo '
			</TABLE>';
	
	} else {
		echo '
			<H3>No Changes Have Been Made</H3>';
	}
}

function task_history_create($field_name,$old_value,$project_task_id) {
	/*
		handle the insertion of history for these parameters
	*/
	$sql="insert into project_history(project_task_id,field_name,old_value,mod_by,date) ".
		"VALUES ('$project_task_id','$field_name','$old_value','".user_getid()."','".time()."')";
	$result=db_query($sql);
	if (!$result) {
		echo "\n<H1>Error inserting history for $field_name</H1>";
		echo db_error();
	}
}

?>
