<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: stats_function.php,v 1.14 2000/01/13 18:36:35 precision Exp $

require('HTML_Graphs.php');

function stats_sf_stats() {
	global $sys_datefmt;
/*
	pages/day
*/
	$sql="SELECT * FROM stats_agg_pages_by_day";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Page Views By Day');
	}

	echo '<P>';

/*
	pages/hour
*/
	$sql="SELECT * FROM stats_agg_pages_by_hour";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Page Views By Hour');
	}
	echo '<P>';

/*
	Groups added by week
*/
	$sql="select (round((register_time/604800),0)*604800) AS time ,count(*) from groups group by time";
	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		$count=array();
		$dates=array();
		$count=result_column_to_array($result,1);

		for ($i=0;$i<$rows;$i++) {
			//convert the dates and add to an array
			$dates[$i]=date($sys_datefmt,db_result($result,$i,0));
		}
		GraphIt($dates,$count,'New Projects Added Each Week');
	}
	echo '<P>';

/*
	Users added by week
*/
	$sql="select (round((add_date/604800),0)*604800) AS time ,count(*) from user group by time";
	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		$count=array();
		$dates=array();
		$count=result_column_to_array($result,1);

		for ($i=0;$i<$rows;$i++) {
			//convert the dates and add to an array
			$dates[$i]=date($sys_datefmt,db_result($result,$i,0));
		}
		GraphIt($dates,$count,'New Users Added Each Week');
	}
	echo '<P>';

}


function stats_project_stats() {
/*
	logo impressions/day
*/
	$sql="SELECT * FROM stats_agg_logo_by_day";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Logo Showings By Day');
	}

	echo '<P>';

/*
	logo impressions/group
*/
	$sql="SELECT * FROM stats_agg_logo_by_group";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Logo Showings By Project');
	}

	echo '<P>';

}


function stats_browser_stats() {
/*
	Browser
*/
	$sql="SELECT * FROM stats_agg_pages_by_browser";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Page Views By Browser');
	}
	echo '<P>';

/*
	Platform
*/
	$sql="SELECT * FROM stats_agg_pages_by_platform";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		GraphResult($result,'Page Views By Platform');
	}
	echo '<P>';

/*
	Browser/ver
*/
	$sql="SELECT * FROM stats_agg_pages_by_plat_brow_ver";

	$result = db_query ($sql);
	$rows = db_numrows($result);

	if (!$result || $rows < 1) {
		echo '<H1>Stats Problem</H1>';
		echo db_error();
	} else {
		ShowResultSet($result,'Page Views By Platform/Browser Version');
	}
	echo '<P>';
}

?>
