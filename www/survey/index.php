<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.7 2000/01/30 09:54:28 precision Exp $

require('pre.php');
require('vote_function.php');
require('../survey/survey_utils.php');

survey_header(array('title'=>'Survey'));

if (!$group_id) {
	echo "<H1>For some reason, the Group ID or Survey ID did not make it to this page</H1>";
}

Function  ShowResultsGroupSurveys($result) {
	global $group_id;
	$rows  =  db_numrows($result);
	$cols  =  db_numfields($result);

	echo /*"<TABLE BGCOLOR=\"NAVY\"><TR><TD BGCOLOR=\"NAVY\">*/ "<table border=0>\n";
	/*  Create  the  headers  */
	echo "<tr BGCOLOR=\"$GLOBALS[COLOR_MENUBARBACK]\">\n";
	echo "<th><FONT COLOR=\"WHITE\"><B>Survey ID</th><th><FONT COLOR=\"WHITE\"><B>Survey Title</th>\n";
	echo "</tr>";

	for($j=0; $j<$rows; $j++)  {

		if ($j%2==0) {
			$row_bg="#FFFFFF";
		} else {
			$row_bg="$GLOBALS[COLOR_LTBACK1]";
		}

		echo "<tr BGCOLOR=\"$row_bg\">\n";

		echo "<TD><A HREF=\"survey.php?group_id=$group_id&survey_id=".db_result($result,$j,"survey_id")."\">".
			db_result($result,$j,"survey_id")."</TD>";

		for ($i=1; $i<$cols; $i++)  {
			printf("<TD>%s</TD>\n",db_result($result,$j,$i));
		}

		echo "</tr>";
	}
	echo "</table>"; //</TD></TR></TABLE>");
}

$sql="SELECT survey_id,survey_title FROM surveys WHERE group_id='$group_id' AND is_active='1'";

$result=db_query($sql);

if (!$result || db_numrows($result) < 1) {
	echo "<H2>This Group Has No Active Surveys</H2>";
	echo db_error();
} else {
	echo "<H2>Surveys for ".group_getname($group_id)."</H2>";
	ShowResultsGroupSurveys($result);
}

survey_footer(array());

?>
