<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: features_boxes.php,v 1.55 2000/01/25 14:06:03 tperdue Exp $


function show_features_boxes() {
	$return .= html_box1_top('SF Statistics',0,$GLOBALS[COLOR_LTBACK2]);
	$return .= show_sitestats();
	$return .= html_box1_middle('Top Project Downloads',$GLOBALS[COLOR_LTBACK2]);
	$return .= show_top_downloads();
	$return .= html_box1_middle('Newest Projects',$GLOBALS[COLOR_LTBACK2]);
	$return .= show_newest_projects();
	$return .= html_box1_middle('Highest Ranked Projects',$GLOBALS[COLOR_LTBACK2]);
	$return .= show_highest_ranked_projects();
	$return .= html_box1_bottom(0);
	return $return;
}

function show_top_downloads() {
	$return .= "<B>Past 7 days:</B>\n";	
	// ############### last week 
	$res_topdown = db_query("SELECT SUM(filerelease.downloads_week) AS downloads_week,"
		. "groups.group_name AS group_name,"
		. "groups.group_id AS group_id "
		. "FROM filerelease,groups WHERE "
		. "filerelease.group_id=groups.group_id "
		. "GROUP BY filerelease.group_id "
		. "ORDER BY downloads_week DESC LIMIT 10");
	// print each one
	while ($row_topdown = db_fetch_array($res_topdown)) {
		if ($row_topdown[downloads_week] > 0) 
			$return .= "<BR><A href=\"/project/?group_id=$row_topdown[group_id]\">"
			. "$row_topdown[group_name]</A> ($row_topdown[downloads_week])\n";
	}

	$return .= "<P><B>All time:</B>\n";
	// ############### all time
	$res_topdown = db_query("SELECT SUM(filerelease.downloads) AS downloads,"
		. "groups.group_name AS group_name,"
		. "groups.group_id AS group_id "
		. "FROM filerelease,groups WHERE "
		. "filerelease.group_id=groups.group_id "
		. "GROUP BY filerelease.group_id "
		. "ORDER BY downloads DESC LIMIT 10");
	// print each one
	while ($row_topdown = db_fetch_array($res_topdown)) {
		if ($row_topdown[downloads] > 0) 
			$return .= "<BR><A href=\"/project/?group_id=$row_topdown[group_id]\">"
			. "$row_topdown[group_name]</A> ($row_topdown[downloads])\n";
	}

	$return .= '<P align="center"><A href="/top/">[More Top Projects]</A>';
	
	return $return;
}

function stats_getprojects_active() {
	$res_count = db_query("SELECT count(*) AS count FROM groups WHERE status='A'");
	if (db_numrows($res_count) > 0) {
		$row_count = db_fetch_array($res_count);
		return $row_count['count'];
	} else {
		return "error";
	}
}

function stats_getprojects_total() {
	$res_count = db_query("SELECT count(*) AS count FROM groups WHERE status='A' OR status='H'");
	if (db_numrows($res_count) > 0) {
		$row_count = db_fetch_array($res_count);
		return $row_count['count'];
	} else {
		return "error";
	}
}

function stats_getusers() {
	$res_count = db_query("SELECT count(*) AS count FROM user WHERE status='A'");
	if (db_numrows($res_count) > 0) {
		$row_count = db_fetch_array($res_count);
		return $row_count['count'];
	} else {
		return "error";
	}
}

function show_sitestats() {
	$return .= 'Your webserver: <B>'.$GLOBALS[sys_name].'</B>';
	$return .= '<P>Hosted Projects: <B>'.stats_getprojects_active().'</B>'
		.' <A href="/search/fullprojectlist.php">[Full List]</A>';
	$return .= '<BR>Registered Users: <B>'.stats_getusers().'</B>';
	return $return;
}

function show_newest_projects() {
	$res_newproj = db_query("SELECT group_id,group_name,register_time FROM groups "
		. "WHERE public=1 AND status IN ('A') "
		. "AND register_time<" . strval(time()-(24*3600)) . " "
		. "ORDER BY register_time DESC LIMIT 10");
	// print each one
	while ($row_newproj = db_fetch_array($res_newproj)) {
		// dont print zero ones (only for launch)
		if ($row_newproj[register_time]) {
			$return .= "<A href=\"/project/?group_id=$row_newproj[group_id]\">"
			. "$row_newproj[group_name]</A> ("
			. date("m/d/Y",$row_newproj[register_time])  . ")<BR>\n";
		}
	}
	return $return;
}

function show_highest_ranked_projects() {
	$sql="SELECT groups.group_name,groups.group_id,survey_rating_aggregate.response,survey_rating_aggregate.count ".
		"FROM groups,survey_rating_aggregate ".
		"WHERE groups.group_id=survey_rating_aggregate.id AND ".
		"groups.public=1 AND survey_rating_aggregate.type='1' AND survey_rating_aggregate.count > '9'".
		"ORDER BY response DESC, count DESC LIMIT 10";
	$result=db_query($sql);
	while ($row=db_fetch_array($result)) {
		$return .= '<B>( '.$row['response'].' )</B>'
			.' <A HREF="/project/?group_id='.$row['group_id'].'">'.$row['group_name'].'</A><BR>';
	}
	return $return;
}

?>
