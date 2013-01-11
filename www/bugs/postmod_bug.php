<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postmod_bug.php,v 1.28 2000/01/13 18:36:34 precision Exp $

$sql="SELECT * FROM bug WHERE bug_id='$bug_id'";

$result=db_query($sql);

if ((db_numrows($result) > 0) && (user_ismember(db_result($result,0,"group_id"),"B2"))) {

	/*
		See which fields changed during the modification
	*/
	if (db_result($result,0,'status_id') != $status_id) { bug_history_create('status_id',db_result($result,0,'status_id'),$bug_id);  }
	if (db_result($result,0,'priority') != $priority) { bug_history_create('priority',db_result($result,0,'priority'),$bug_id);  }
	if (db_result($result,0,'category_id') != $category_id) { bug_history_create('category_id',db_result($result,0,'category_id'),$bug_id);  }
	if (db_result($result,0,'assigned_to') != $assigned_to) { bug_history_create('assigned_to',db_result($result,0,'assigned_to'),$bug_id);  }
	if (stripslashes(db_result($result,0,'summary')) != stripslashes(htmlspecialchars($summary))) 
		{ bug_history_create('summary',htmlspecialchars(addslashes(stripslashes(stripslashes(db_result($result,0,'summary'))))),$bug_id);  }
        if (db_result($result,0,'bug_group_id') != $bug_group_id) { bug_history_create('bug_group_id',db_result($result,0,'bug_group_id'),$bug_id);  }
        if (db_result($result,0,'resolution_id') != $resolution_id) { bug_history_create('resolution_id',db_result($result,0,'resolution_id'),$bug_id);  }

	/*
		Details field is handled a little differently
	*/
	if ($details != '') { bug_history_create('details',addslashes(htmlspecialchars($details)),$bug_id);  }

	/*
		Enter the timestamp if we are changing to closed
	*/
	if ($status_id == "3") {

		$now=time();
		$close_date="close_date='$now',";
		bug_history_create('close_date',db_result($result,0,'close_date'),$bug_id);

	} else {

		$close_date="";

	}

        /*
                DELETE THEN Insert the list of task dependencies
        */
	$task_depend_count=count($dependent_on_task);
        $toss=db_query("DELETE FROM bug_task_dependencies WHERE bug_id='$bug_id'");
        for ($i=0; $i<$task_depend_count; $i++) {
                $sql="INSERT INTO bug_task_dependencies VALUES ('','$bug_id','$dependent_on_task[$i]')";
                //echo "\n$sql";
                $result=db_query($sql);
        }

        /*
                DELETE THEN Insert the list of bug dependencies
        */
        $bug_depend_count=count($dependent_on_bug);
        $toss=db_query("DELETE FROM bug_bug_dependencies WHERE bug_id='$bug_id'");
        for ($i=0; $i<$bug_depend_count; $i++) {
                $sql="INSERT INTO bug_bug_dependencies VALUES ('','$bug_id','$dependent_on_bug[$i]')";
                //echo "\n$sql";
                $result=db_query($sql);
	}

	/*
		Finally, update the bug itself
	*/
	$sql="update bug set status_id='$status_id', $close_date priority='$priority', category_id='$category_id', ".
		"assigned_to='$assigned_to', summary='".htmlspecialchars($summary)."',".
		"bug_group_id='$bug_group_id',resolution_id='$resolution_id' WHERE bug_id='$bug_id'";
	$result=db_query($sql);

	if (!$result) {
		bug_header(array ("title"=>"Bug Modification Failed"));
		echo "<H1>Error - update failed!</H1>";
		echo db_error();
		bug_footer(array());
		exit;
	} else {
		$feedback .= " Successfully Modified Bug ";
	}

	if ($mail_followup) {
		mail_followup($bug_id);
	}

} else {

	exit_permission_denied();

}

?>
