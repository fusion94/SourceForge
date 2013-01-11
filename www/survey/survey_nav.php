<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: survey_nav.php,v 1.9 2000/01/30 09:54:28 precision Exp $ 

html_tabs('surveys',$group_id);

echo "<P><B><A HREF=\"/survey/admin/?group_id=$group_id\">Admin</A>";

if ($is_admin_page && $group_id) {
	echo " | <A HREF=\"/survey/admin/add_survey.php?group_id=$group_id\">Add Surveys</A>";
	echo " | <A HREF=\"/survey/admin/edit_survey.php?group_id=$group_id\">Edit Surveys</A>";
	echo " | <A HREF=\"/survey/admin/add_question.php?group_id=$group_id\">Add Questions</A>";
	echo " | <A HREF=\"/survey/admin/show_questions.php?group_id=$group_id\">Edit Questions</A>";
	echo " | <A HREF=\"/survey/admin/show_results.php?group_id=$group_id\">Show Results</A></B>";
}

echo "<P>";

?>
