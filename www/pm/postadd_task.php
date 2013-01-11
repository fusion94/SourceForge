<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_task.php,v 1.6 2000/01/13 18:36:36 precision Exp $

if (mktime(0,0,0,$start_month,$start_day,$start_year) > mktime(0,0,0,$end_month,$end_day,$end_year)) {
	pm_header(array('title'=>'Error'));
	echo '<H1>Error - End Date Must Be Greater Than Begin Date</H1>';
	pm_footer(array());
	exit;
}

$sql="INSERT INTO project_task (group_project_id,summary,details,percent_complete,".
	"priority,hours,start_date,end_date,".
	"created_by,status_id) VALUES ('$group_project_id','".htmlspecialchars($summary)."',".
	"'".htmlspecialchars($details)."','$percent_complete','$priority','$hours','".
	mktime(0,0,0,$start_month,$start_day,$start_year)."','".
	mktime(0,0,0,$end_month,$end_day,$end_year)."','".user_getid()."','1')";

//echo "\n$sql";

$result=db_query($sql);
if (!$result) {
	$feedback .= ' ERROR INSERTING ROW ';
} else {
	$feedback .= ' Successfully added task ';
}

$project_task_id=db_insertid($result);
$user_count=count($assigned_to);
$depend_count=count($dependent_on);

/*
	Insert the people this task is assigned to
*/
for ($i=0; $i<$user_count; $i++) {
	$sql="INSERT INTO project_assigned_to VALUES ('','$project_task_id','$assigned_to[$i]')";
	//echo "\n$sql";
	$result=db_query($sql);
}

/*
	Insert the list of dependencies
*/
for ($i=0; $i<$depend_count; $i++) {
	$sql="INSERT INTO project_dependencies VALUES ('','$project_task_id','$dependent_on[$i]')";
	//echo "\n$sql";
	$result=db_query($sql);
}

?>
