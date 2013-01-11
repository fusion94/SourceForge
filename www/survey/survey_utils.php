<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id:$

function survey_header($params) {
	global $group_id,$is_admin_page,$DOCUMENT_ROOT;
	$params['group']=$group_id;
	site_header($params);
	include($DOCUMENT_ROOT.'/survey/survey_nav.php');
}

function survey_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

?>
