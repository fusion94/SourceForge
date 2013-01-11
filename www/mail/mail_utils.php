<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mail_utils.php,v 1.5 2000/01/13 18:36:35 precision Exp $

function mail_header($params) {
	global $group_id,$is_admin_page,$DOCUMENT_ROOT;
	$params['group']=$group_id;
	site_header($params);
}

function mail_footer($params) {
	global $feedback;
	html_feedback_bottom($feedback);
	site_footer($params);
}

?>
