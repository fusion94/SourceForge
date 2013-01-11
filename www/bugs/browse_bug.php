<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: browse_bug.php,v 1.35 2000/01/26 16:54:11 tperdue Exp $

bug_header(array ('title'=>'Browse Bugs'));

if (!$offset || $offset < 0) {
	$offset=0;
}

//
// Memorize order by field as a user preference if explicitly specified.
// Automatically discard invalid field names.
//
if ($order) {
	if ($order=='bug_id' || $order=='summary' || $order=='date' || $order=='assigned_to_user' || $order=='submitted_by') {
		if(user_isloggedin()) {
			user_set_preference('bug_browse_order', $order);
		}
	} else {
		$order = false;
	}
} else {
	if(user_isloggedin()) {
		$order = user_get_preference('bug_browse_order');
	}
}

if ($order) {
	$order_by = " ORDER BY $order ";
} else {
	$order_by = "";
}

if ($set=='my') {
	/*
		View this individual's bugs, whether submitted by or assigned to him
	*/
	$sql="SELECT bug.group_id,bug.priority,bug.bug_id,bug.summary,bug.date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM bug,user,user user2 ".
		"WHERE bug.status_id <> '3' ".
		"AND user.user_id=bug.submitted_by ".
		"AND user2.user_id=bug.assigned_to ".
		"AND (bug.assigned_to='".user_getid()."' ".
		"OR bug.submitted_by='".user_getid()."') ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement="Viewing bugs submitted by or assigned to you";

} else if ($set=='closed') {
	/*
		Browse the closed bugs in this group
	*/
	$sql="SELECT bug.group_id,bug.priority,bug.bug_id,bug.summary,bug.close_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM bug,user,user user2 ".
		"WHERE user.user_id=bug.submitted_by ".
		"AND bug.status_id='3' ".
		"AND user2.user_id=bug.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement="Viewing Closed Bugs";

} else if (user_isloggedin()) {
	/*
		Check and see if this user has a filter set up
	*/
	$sql="SELECT sql_clause FROM bug_filter WHERE user_id='".user_getid()."' AND group_id='$group_id' AND is_active='1'";

	$result=db_query($sql);

	if ($result && db_numrows($result) > 0) {
		$sql="SELECT bug.group_id,bug.priority,bug.bug_id,bug.summary,bug.date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
			"FROM bug,user,user user2 ".
			"WHERE (".stripslashes(db_result($result,0,"sql_clause")).") ".
			"AND user.user_id=bug.submitted_by ".
			"AND user2.user_id=bug.assigned_to ".
			"AND group_id='$group_id'".
			$order_by .
			" LIMIT $offset,50";

		$statement="Using Your Filter";

	} else {
		/*
			Just browse the bugs in this group
		*/
		$sql="SELECT bug.group_id,bug.priority,bug.bug_id,bug.summary,bug.date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
			"FROM bug,user,user user2 ".
			"WHERE user.user_id=bug.submitted_by ".
			"AND bug.status_id <> '3' ".
			"AND user2.user_id=bug.assigned_to ".
			"AND group_id='$group_id'".
			$order_by .
			" LIMIT $offset,50";

		$statement="Viewing Open Bugs";
	}

} else {
	/*
		Just browse the bugs in this group
	*/
	$sql="SELECT bug.group_id,bug.priority,bug.bug_id,bug.summary,bug.date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM bug,user,user user2 ".
		"WHERE user.user_id=bug.submitted_by ".
		"AND bug.status_id <> '3' ".
		"AND user2.user_id=bug.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement="Viewing Open Bugs";
}

$result=db_query($sql);

if ($result && db_numrows($result) > 0) {

	echo "<h3>$statement</H3>";

	show_buglist($result,$offset,$set);

	show_priority_colors_key();

} else {
	echo "<H3>$statement</H3>";
	echo "\n<H1>No Bugs Found for ".group_getname($group_id)." or filters too restrictive</H1>";
	echo db_error();
}

bug_footer(array());

?>
