<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_comment.php,v 1.3 2000/01/26 15:35:22 tperdue Exp $

if ($details != '') { 
	bug_history_create('details',addslashes(htmlspecialchars($details)),$bug_id);  
	mail_followup($bug_id);
	$feedback .= ' Comment added to bug ';
}

$feedback .= ' Nothing Done ';

?>
