<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: browse_task.php,v 1.31 2000/06/16 08:57:11 tperdue Exp $

if (!$offset || $offset < 0) {
	$offset=0;
}

//
// Memorize order by field as a user preference if explicitly specified.
// Automatically discard invalid field names.
//
if ($order) {
	if ($order=='project_task_id' || $order=='percent_complete' || $order=='summary' || $order=='start_date' || $order=='end_date' || $order=='priority') {
		if(user_isloggedin()) {
			user_set_preference('pm_task_order', $order);
		}
	} else {
		$order = false;
	}
} else {
	if(user_isloggedin()) {
		$order = user_get_preference('pm_task_order');
	}
}

if ($order) {
	//if ordering by priority, sort DESC
	$order_by = " ORDER BY project_task.$order".(($order=='priority') ? ' DESC ':' ');
} else {
	$order_by = "";
}

//the default is to show 'my' tasks, not 'open' as it used to be
if (!$set) {
	/*
		if no set is passed in, see if a preference was set
		if no preference or not logged in, use open set
	*/
	if (user_isloggedin()) {
		$custom_pref=user_get_preference('pm_brow_cust'.$group_id);
		if ($custom_pref) {
			$pref_arr=explode('|',$custom_pref);
			$_assigned_to=$pref_arr[0];
			$_status=$pref_arr[1];
			$set='custom';
		} else {
			$set='my';
		}
	} else {
		$set='open';
	}
}

if ($set=='my') {
	/*
		My tasks - backwards compat can be removed 9/10
	*/
	$_status=1;
	$_assigned_to=user_getid();

} else if ($set=='custom') {
	/*
		if this custom set is different than the stored one, reset preference
	*/
	$pref_=$_assigned_to.'|'.$_status;
	if ($pref_ != user_get_preference('pm_brow_cust'.$group_id)) {
		//echo 'setting pref';
		user_set_preference('pm_brow_cust'.$group_id,$pref_);
	}
} else if ($set=='closed') {
	/*
		Closed tasks - backwards compat can be removed 9/10
	*/
	unset($_assigned_to);
	$_status='2';
} else {
	/*
		Open tasks - backwards compat can be removed 9/10
	*/
	unset($_assigned_to);
	$_status='1';
}

/*
	Display tasks based on the form post - by user or status or both
*/

//if status selected, and more to where clause
if ($_status && ($_status != 100)) {
	//for open tasks, add status=100 to make sure we show all
	$status_str="AND project_task.status_id IN ($_status".(($_status==1)?',100':'').")";
} else {
	//no status was chosen, so don't add it to where clause
	$status_str='';
}

//if assigned to selected, and more to where clause
if ($_assigned_to && ($_assigned_to != 100)) {
	$assigned_str="AND project_assigned_to.assigned_to_id='$_assigned_to'";

	//workaround for old tasks that do not have anyone assigned to them
	//should not be needed for tasks created/updated after may, 2000
	$assigned_str2=',project_assigned_to';
	$assigned_str3='project_task.project_task_id=project_assigned_to.project_task_id AND';
	
} else {
	//no assigned to was chosen, so don't add it to where clause
	$assigned_str='';
}

//build page title to make bookmarking easier
//if a user was selected, add the user_name to the title
//same for status
pm_header(array('title'=>'Browse Tasks'.
	(($_assigned_to && ($_assigned_to != 100))?' For: '.user_getname($_assigned_to):'').
	(($_status && ($_status != 100))?' By Status: '.pm_data_get_status_name($_status):'')));

$sql="SELECT project_task.priority,project_task.group_project_id,project_task.project_task_id,".
	"project_task.start_date,project_task.end_date,project_task.percent_complete,project_task.summary ".
	"FROM project_task $assigned_str2 ".
	"WHERE $assigned_str3 project_task.group_project_id='$group_project_id' ".
	" $assigned_str $status_str ".
	$order_by .
	" LIMIT $offset,50";

$message="Browsing Custom Task List";

$result=db_query($sql);

/*
	Show the new pop-up boxes to select assigned to and/or status
*/
echo '<TABLE WIDTH="10%" BORDER="0"><FORM ACTION="'. $PHP_SELF .'" METHOD="GET">
	<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="'.$group_id.'">
	<INPUT TYPE="HIDDEN" NAME="set" VALUE="custom">
	<INPUT TYPE="HIDDEN" NAME="group_project_id" VALUE="'.$group_project_id.'">
	<TR><TD COLSPAN="3" nowrap><b>Browse Tasks by User and/or Status:</b></TD></TR>
	<TR><TD><FONT SIZE="-1">';
echo pm_tech_select_box('_assigned_to',$group_id,$_assigned_to);
echo '</TD><TD><FONT SIZE="-1">'. pm_status_box('_status',$_status) .'</TD>'.
'<TD><FONT SIZE="-1"><INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="Browse"></TD></TR></FORM></TABLE>';


if (db_numrows($result) < 1) {

	echo '
		<H1>No Matching Tasks found</H1>
		<P>
		<B>Add tasks using the link above</B>';
	echo db_error();

} else {

	//create a new $set string to be used for next/prev button
	if ($set=='custom') {
		$set .= '&_assigned_to='.$_assigned_to.'&_status='.$_status;
	}

	/*
		Now display the tasks in a table with priority colors
	*/

	echo '
		<br>
		<H3>'.$message.' In '. pm_data_get_group_name($group_project_id) .'</H3>';
	pm_show_tasklist($result,$offset,$set);
	echo '<P>* Denotes overdue tasks';
	show_priority_colors_key();
	$url = "/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=$set&order=";
	echo '<P>Click a column heading to sort by that column, or <A HREF="'.$url.'priority">Sort by Priority</A>';

}

pm_footer(array());

?>
