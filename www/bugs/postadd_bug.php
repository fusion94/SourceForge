<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_bug.php,v 1.18 2000/01/13 18:36:34 precision Exp $

if (!$category_id) {
	$category_id=100;
}

if (!$bug_group_id) {
	$bug_group_id=100;
}

if (!user_isloggedin()) {
	$user=100;
} else {
	$user=user_getid();
}

$sql="INSERT INTO bug (close_date,group_id,status_id,priority,category_id,submitted_by,assigned_to,date,summary,details,bug_group_id,resolution_id) ".
	"VALUES ('0','$group_id','100','5','$category_id','$user','100','".time()."','".htmlspecialchars($summary)."','".htmlspecialchars($details)."','$bug_group_id','100')";

$result=db_query($sql);

if (!$result) {

	bug_header(array ("title"=>"Bug Submission Failed"));
	echo "<H1>Error - Go Back and Try Again!</H1>";
	echo db_error();
	bug_footer(array());
	exit;

} else {
	$feedback .= " Successfully Added Bug ";
}

?>
