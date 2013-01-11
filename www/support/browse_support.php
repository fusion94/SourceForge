<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: browse_support.php,v 1.7 2000/04/21 14:10:53 tperdue Exp $

support_header(array ('title'=>'Browse Support Requests'));

if (!$offset || $offset < 0) {
	$offset=0;
}

//
// Memorize order by field as a user preference if explicitly specified.
// Automatically discard invalid field names.
//
if ($order) {
	if ($order=='support_id' || $order=='summary' || $order=='date' || $order=='assigned_to_user' || $order=='submitted_by') {
		if(user_isloggedin()) {
			user_set_preference('support_browse_order', $order);
		}
	} else {
		$order = false;
	}
} else {
	if(user_isloggedin()) {
		$order = user_get_preference('support_browse_order');
	}
}

if ($order) {
	$order_by = " ORDER BY $order ".(($set=='closed' && $order=='date') ? ' DESC ':'');
} else {
	$order_by = "";
}

if ($set=='my') {
	/*
		View this individual's supportes, whether submitted by or assigned to him
	*/
	$sql="SELECT support.priority,support.group_id,support.support_id,support.summary,".
		"support.open_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM support,user,user user2 ".
		"WHERE support.support_status_id='1' ".
		"AND user.user_id=support.submitted_by ".
		"AND user2.user_id=support.assigned_to ".
		"AND (support.assigned_to='".user_getid()."' ".
		"OR support.submitted_by='".user_getid()."') ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Support Requests submitted by or assigned to you';

} else if ($set=='closed') {
	/*
		Browse the closed supportes in this group
	*/
	$sql="SELECT support.priority,support.group_id,support.support_id,support.summary,".
		"support.close_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM support,user,user user2 ".
		"WHERE user.user_id=support.submitted_by ".
		"AND support.support_status_id='2' ".
		"AND user2.user_id=support.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Closed Support Requests';

} else {
	/*
		Just browse the supportes in this group
	*/
	$sql="SELECT support.priority,support.group_id,support.support_id,support.summary,".
		"support.open_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM support,user,user user2 ".
		"WHERE user.user_id=support.submitted_by ".
		"AND support.support_status_id ='1' ".
		"AND user2.user_id=support.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Open Support Requests';
}

$result=db_query($sql);

if ($result && db_numrows($result) > 0) {

	echo '
		<h3>'.$statement.'</H3>';

	echo '
		<P>
		<B>You can use the Support Manager to coordinate tech support</B>
		<P>';

	show_supportlist($result,$offset,$set);

	echo '* Denotes Requests > 15 Days Old';
	show_priority_colors_key();

} else {
	echo '
		<H3>'.$statement.'</H3>
		<P>
		<B>You can use the Support Manager to coordinate tech support</B>
		<P>';
	echo '
		<H1>No Such Support Requests Found for '.group_getname($group_id).'</H1>';
	echo db_error();
	echo "<!-- $sql -->";
}

support_footer(array());

?>
