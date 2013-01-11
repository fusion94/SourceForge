<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: survey_utils.php,v 1.11 2000/01/30 09:54:28 precision Exp $

/*
	Survey System
	By Tim Perdue, Sourceforge, 11/99
*/

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
