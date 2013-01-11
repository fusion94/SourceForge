<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: get_session_hash.php,v 1.2 2000/04/14 11:47:10 tperdue Exp $

require ('squal_pre.php');

/*

	MUST USE SSL

	params: $user, $pass

	returns: either valid session_hash or ERROR string

*/

if (!session_issecure()) {
	//force use of SSL for login
	echo 'ERROR - MUST USE SSL';
	exit;
}

if (session_login_valid($user,$pass)) {
	echo $session_hash;
} else {
	echo 'ERROR - '.error_get_string();
}

?>
