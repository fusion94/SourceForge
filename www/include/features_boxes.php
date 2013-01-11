<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: features_boxes.php,v 1.77 2000/06/17 08:24:35 tperdue Exp $


function show_features_boxes() {
	$return .= html_box1_top('SF Statistics',0,$GLOBALS['COLOR_LTBACK2']);
	$return .= show_sitestats();
	$return .= html_box1_middle('Top Project Downloads',$GLOBALS['COLOR_LTBACK2']);
	$return .= show_top_downloads();
	$return .= html_box1_middle('Newest Projects',$GLOBALS['COLOR_LTBACK2']);
	$return .= show_newest_projects();
	$return .= html_box1_middle('Most Active This Week',$GLOBALS['COLOR_LTBACK2']);
	$return .= show_highest_ranked_projects();
	$return .= html_box1_bottom(0);
	return $return;
}

function show_top_downloads() {
	$return .= "<B>Downloads Yesterday:</B>\n";	

	#get yesterdays day
	$yesterday = gmdate("Ymd",time()-(3600*24));

	$res_topdown = db_query("SELECT groups.group_id,"
		."groups.group_name,"
		."groups.unix_group_name,"
		."frs_dlstats_group_agg.downloads "
		."FROM frs_dlstats_group_agg,groups WHERE day=$yesterday "
		."AND frs_dlstats_group_agg.group_id=groups.group_id "
		."ORDER BY downloads DESC LIMIT 10");
	// print each one
	while ($row_topdown = db_fetch_array($res_topdown)) {
		if ($row_topdown['downloads'] > 0) 
			$return .= "<BR><A href=\"/projects/$row_topdown[unix_group_name]/\">"
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
	$return .= 'Your webserver: <B>'.$GLOBALS['sys_name'].'</B>';
	$return .= '<P>Hosted Projects: <B>'.stats_getprojects_active().'</B>';
	$return .= '<BR>Registered Users: <B>'.stats_getusers().'</B>';
	return $return;
}

function show_newest_projects() {
	$res_newproj = db_query("SELECT group_id,unix_group_name,group_name,register_time FROM groups "
		. "WHERE is_public=1 AND status IN ('A') "
		. "AND register_time<" . strval(time()-(24*3600)) . " "
		. "ORDER BY register_time DESC LIMIT 10");
	// print each one
	while ($row_newproj = db_fetch_array($res_newproj)) {
		// dont print zero ones (only for launch)
		if ($row_newproj['register_time']) {
			$return .= "<A href=\"/projects/$row_newproj[unix_group_name]/\">"
			. "$row_newproj[group_name]</A> ("
			. date("m/d/Y",$row_newproj['register_time'])  . ")<BR>\n";
		}
	}
	return $return;
}

function show_highest_ranked_projects() {
	$sql="SELECT groups.group_name,groups.unix_group_name,groups.group_id,project_weekly_metric.ranking,project_weekly_metric.percentile ".
		"FROM groups,project_weekly_metric ".
		"WHERE groups.group_id=project_weekly_metric.group_id AND ".
		"groups.is_public=1 ".
		"ORDER BY ranking ASC LIMIT 20";
	$result=db_query($sql);
	if (!$result || db_numrows($result) < 1) {
		return db_error();
	} else {
		while ($row=db_fetch_array($result)) {
			$return .= '<B>( '.$row['percentile'].'% )</B>'
				.' <A HREF="/projects/'.$row['unix_group_name'].'/">'.$row['group_name'].'</A><BR>';
		}
		$return .= '<BR><CENTER><A href="/top/mostactive.php?type=week">[ more ]</A></CENTER>';
	}
	return $return;
}

?>
