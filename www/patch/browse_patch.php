<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: browse_patch.php,v 1.10 2000/01/26 16:20:34 tperdue Exp $

patch_header(array ('title'=>'Browse Patches'));

if (!$offset || $offset < 0) {
	$offset=0;
}

//
// Memorize order by field as a user preference if explicitly specified.
// Automatically discard invalid field names.
//
if ($order) {
	if ($order=='patch_id' || $order=='summary' || $order=='date' || $order=='assigned_to_user' || $order=='submitted_by') {
		if(user_isloggedin()) {
			user_set_preference('patch_browse_order', $order);
		}
	} else {
		$order = false;
	}
} else {
	if(user_isloggedin()) {
		$order = user_get_preference('patch_browse_order');
	}
}

if ($order) {
	$order_by = " ORDER BY $order ";
} else {
	$order_by = "";
}

if ($set=='my') {
	/*
		View this individual's patches, whether submitted by or assigned to him
	*/
	$sql="SELECT patch.group_id,patch.patch_id,patch.summary,".
		"patch.open_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM patch,user,user user2 ".
		"WHERE patch.patch_status_id='1' ".
		"AND user.user_id=patch.submitted_by ".
		"AND user2.user_id=patch.assigned_to ".
		"AND (patch.assigned_to='".user_getid()."' ".
		"OR patch.submitted_by='".user_getid()."') ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing patches submitted by or assigned to you';

} else if ($set=='closed') {
	/*
		Browse the closed patches in this group
	*/
	$sql="SELECT patch.group_id,patch.patch_id,patch.summary,".
		"patch.close_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM patch,user,user user2 ".
		"WHERE user.user_id=patch.submitted_by ".
		"AND patch.patch_status_id='2' ".
		"AND user2.user_id=patch.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Closed Patches';

} else if ($set=='postponed') {
	/*
		Browse the postponed patches in this group
	*/
	$sql="SELECT patch.group_id,patch.patch_id,patch.summary,".
		"patch.close_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM patch,user,user user2 ".
		"WHERE user.user_id=patch.submitted_by ".
		"AND patch.patch_status_id='4' ".
		"AND user2.user_id=patch.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Postponed Patches';

} else {
	/*
		Just browse the patches in this group
	*/
	$sql="SELECT patch.group_id,patch.patch_id,patch.summary,".
		"patch.open_date AS date,user.user_name AS submitted_by,user2.user_name AS assigned_to_user ".
		"FROM patch,user,user user2 ".
		"WHERE user.user_id=patch.submitted_by ".
		"AND patch.patch_status_id ='1' ".
		"AND user2.user_id=patch.assigned_to ".
		"AND group_id='$group_id'".
		$order_by .
		" LIMIT $offset,50";

	$statement='Viewing Open Patches';
}

$result=db_query($sql);

if ($result && db_numrows($result) > 0) {

	echo '
		<h3>'.$statement.'</H3>';

	echo '
		<P>
		<B>You can use the Patch Manager to control/faciliate code contributions from the user community</B>
		<P>';

	show_patchlist($result,$offset,$set);

} else {
	echo '
		<H3>'.$statement.'</H3>
		<P>
		<B>You can use the Patch Manager to control/faciliate code contributions from the user community</B>
		<P>';
	echo '
		<H1>No Such Patches Found for '.group_getname($group_id).'</H1>';
	echo db_error();
}

patch_footer(array());

?>
