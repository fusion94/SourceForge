<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postmod_task.php,v 1.11 2000/01/13 18:36:36 precision Exp $

$sql="SELECT * FROM project_task WHERE project_task_id='$project_task_id' AND group_project_id='$group_project_id'";

$result=db_query($sql);

if ((db_numrows($result) > 0)) {

	if (mktime(0,0,0,$start_month,$start_day,$start_year) > mktime(0,0,0,$end_month,$end_day,$end_year)) {
		pm_header(array('title'=>'Error'));
		echo '<H1>Error - End Date Must Be Greater Than Begin Date</H1>';
		pm_footer(array());
		exit;
	}
	/*
		See which fields changed during the modification
	*/

	if (db_result($result,0,'status_id') != $status_id) 
		{ task_history_create('status_id',db_result($result,0,'status_id'),$project_task_id);  }

	if (db_result($result,0,'priority') != $priority) 
		{ task_history_create('priority',db_result($result,0,'priority'),$project_task_id);  }

	if (stripslashes(db_result($result,0,'summary')) != htmlspecialchars(stripslashes($summary))) 
		{ task_history_create('summary',htmlspecialchars(addslashes(stripslashes(stripslashes(db_result($result,0,'summary'))))),$project_task_id);  }

	if (db_result($result,0,'percent_complete') != $percent_complete)
		{ task_history_create('percent_complete',db_result($result,0,'percent_complete'),$project_task_id);  }

	if (db_result($result,0,'hours') != $hours)
		{ task_history_create('hours',db_result($result,0,'hours'),$project_task_id);  }

	if (db_result($result,0,'start_date') != mktime(0,0,0,$start_month,$start_day,$start_year))
		{ task_history_create('start_date',db_result($result,0,'start_date'),$project_task_id);  }

	if (db_result($result,0,'end_date') != mktime(0,0,0,$end_month,$end_day,$end_year))
		{ task_history_create('end_date',db_result($result,0,'end_date'),$project_task_id);  }

	/*
		Details field is handled a little differently
	*/
	if ($details != "") { task_history_create('details',addslashes(htmlspecialchars($details)),$project_task_id);  }

#
#
#
#
#		The audit trail should someday include changes to users/dependencies,
#		but it doesn't yet
#
#
#
	$user_count=count($assigned_to);
	$depend_count=count($dependent_on);
	/*
		DELETE THEN Insert the people this task is assigned to
	*/
	$toss=db_query("DELETE FROM project_assigned_to WHERE project_task_id='$project_task_id'");
	for ($i=0; $i<$user_count; $i++) {
		$sql="INSERT INTO project_assigned_to VALUES ('','$project_task_id','$assigned_to[$i]')";
		//echo "\n$sql";
		$result=db_query($sql);
	}

	/*
		DELETE THEN Insert the list of dependencies
	*/
	$toss=db_query("DELETE FROM project_dependencies WHERE project_task_id='$project_task_id'");
	for ($i=0; $i<$depend_count; $i++) {
		$sql="INSERT INTO project_dependencies VALUES ('','$project_task_id','$dependent_on[$i]')";
		//echo "\n$sql";
		$result=db_query($sql);
	}


	$sql="UPDATE project_task SET status_id='$status_id', priority='$priority',".
		"summary='".htmlspecialchars($summary)."',start_date='".
		mktime(0,0,0,$start_month,$start_day,$start_year)."',end_date='".
		mktime(0,0,0,$end_month,$end_day,$end_year)."',hours='$hours',".
		"percent_complete='$percent_complete' ".
		"WHERE project_task_id='$project_task_id' AND group_project_id='$group_project_id'";

	$result=db_query($sql);

	if (!$result) {
		pm_header(array ('title'=>'Task Modification Failed'));
		echo '<H1>Error - update failed!</H1>';
		echo db_error();
		pm_footer(array());
		exit;
	} else {
		$feedback .= ' Successfully Modified Task ';
	}

} else {

	pm_header(array ('title'=>'Task Modification Failed'));
	echo '<H1>Task Not Found</H1>';
	echo db_error();
	pm_footer(array());
	exit;

}

?>
