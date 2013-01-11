<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: postmod_support.php,v 1.12 2000/05/05 21:58:31 tperdue Exp $

	if ($mail_followup) {
		mail_followup($support_id);
	}

?>
