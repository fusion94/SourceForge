<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_comment.php,v 1.2 2000/01/13 18:36:34 precision Exp $

if ($details != '') { 
	bug_history_create('details',addslashes(htmlspecialchars($details)),$bug_id);  
}

$feedback .= ' Comment added to bug ';

?>
