<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: browse_task.php,v 1.8 2000/01/13 18:36:36 precision Exp $

pm_header(array('title'=>'Browse Tasks'));

if (!$offset || $offset < 0) {
	$offset=0;
}

if ($set=='my') {
	$sql="SELECT project_task.priority,project_task.group_project_id,project_task.project_task_id,".
		"project_task.start_date,project_task.end_date,project_task.percent_complete,project_task.summary ".
		"FROM project_task,project_assigned_to ".
		"WHERE project_task.project_task_id=project_assigned_to.project_task_id AND project_task.group_project_id='$group_project_id' ".
		"AND project_assigned_to.assigned_to_id='".user_getid()."' AND project_task.status_id='1' LIMIT $offset,50";
	$message="Browsing My Tasks";
} else if ($set=='closed') {
	$sql="SELECT priority,project_task_id,group_project_id,summary,start_date,end_date,percent_complete ".
		"FROM project_task WHERE group_project_id='$group_project_id' AND status_id='2'  LIMIT $offset,50";
	$message="Browsing Closed Tasks";
} else {
	$sql="SELECT priority,project_task_id,group_project_id,summary,start_date,end_date,percent_complete ".
		"FROM project_task WHERE group_project_id='$group_project_id' AND status_id='1'  LIMIT $offset,50";
	$message="Browsing Open Tasks";
	$set='open';
}

$result=db_query($sql);

if (db_numrows($result) < 1) {
	echo '
		<H1>None found</H1>
		<P><B>Add tasks using the link above</B>';
	echo db_error();
} else {
	echo '
		<H3>'.$message.' In '.get_task_group_name($group_project_id).'</H3>';
	show_tasklist($result,$offset,$set);

	show_priority_colors_key();
}

pm_footer(array());
?>
