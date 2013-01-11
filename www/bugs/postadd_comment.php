<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postadd_comment.php,v 1.5 2000/04/17 11:00:46 tperdue Exp $

if ($details != '') { 

	bug_data_add_history ('details',htmlspecialchars($details),$bug_id);  
	mail_followup($bug_id);
	$feedback .= ' Comment added to bug ';

} else {

	$feedback .= ' Nothing Done ';

}

?>
