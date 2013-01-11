<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: session.php,v 1.81 2000/01/13 18:36:35 precision Exp $
//

// ################################ function session_issecure()
// ## binary. returns true if session is SSL

function session_issecure() {
	return (getenv('SERVER_PORT') == '443');
}

// ############################ function session_cookie()

function session_cookie($n,$v) {
	setcookie($n,$v,0,'/','',0);
}

// ######################### function session_redirect()

function session_redirect($loc) {
	header('Location: http' . (session_issecure()?'s':'') . '://' . getenv('HTTP_HOST') . $loc);
	print("\n\n");
	exit;
}

/// ************************ function session_securitylog

function session_securitylog($cat,$desc) {
	db_query("INSERT INTO security_log (session_hash,time,user_id,category,description) "
		. "VALUES ("
		. "'" . $GLOBALS[session_hash] . "',"
		. time() . ","
		. user_getid() . ","
		. "'" . $cat . "',"
		. "'" . $desc . "')");
}

// ######################### function session_require

function session_require($req) {
	if (user_ismember(1)) { return 1; }

	if ($req[group]) {
		$query = "SELECT user_id FROM user_group WHERE user_id=" . user_getid()
			. " AND group_id=$req[group]";
		if ($req[admin_flags]) {
		$query .= " AND admin_flags = '$req[admin_flags]'";	
		}
 
		if ((db_numrows(db_query($query)) < 1) || !$req[group]) {
			exit_error("Insufficient Group Access","You do not have permission to "
				. "view this page.");
		}
	}
	elseif ($req[user]) {
		if (user_getid() != $req[user]) {	
			exit_error("Insufficient User Access","You do not have permission to "
				. "view this page.");
		}
	}
	elseif ($req[isloggedin]) {
		if (!user_isloggedin()) {
			exit_error("Required Login","In order to view this page, you must "
				. "be logged in.");
		}
	} else {
		exit_error("Insufficient Access","Probably by mangling a URL, you have attempted "
			. "to reach a part of the site for which you do not have access. This can "
			. "probably be fixed by properly navigating through the site.");
	}
}

// ######################### function session_setglobals()

function session_setglobals() {
	global $G_SESSION,$G_USER;

	unset($G_USER);

	if ($G_SESSION[user_id]) {
		db_query("SELECT user_id,user_name FROM user WHERE user_id=$G_SESSION[user_id]");
		$G_USER = db_fetch_array();
	} else {
		$G_USER = 0;
	}
}

// ######################## function session_set_new()

function session_set_new() {
	global $G_SESSION;

	unset($G_SESSION);

	// concatinate current time, and random seed for MD5 hash
	// continue until unique hash is generated (SHOULD only be once)
	do {
		$pre_hash = strval(time()) . strval(rand());
		$GLOBALS[session_hash] = md5($pre_hash);
	} while (db_numrows(db_query("SELECT session_hash FROM session WHERE session_hash='$GLOBALS[session_hash]'")) > 0);
		
	// set session cookie
	session_cookie("session_hash",$GLOBALS[session_hash]);

	// make new session entries into db
	db_query("INSERT INTO session (session_hash, ip_addr, time) VALUES "
		. "('$GLOBALS[session_hash]','$GLOBALS[REMOTE_ADDR]'," . time() . ")");

	// set global
	$G_SESSION = db_fetch_array(db_query("SELECT * FROM session WHERE session_hash=$GLOBALS[session_hash]"));
}

// ###################### function session_set()

function session_set() {
	global $G_SESSION;

	unset($G_SESSION);

	// assume bad session_hash and session. If all checks work, then allow
	// otherwise make new session
	$id_is_good = 0;
	
	// here also check for good hash, set if new session is needed
	if ($GLOBALS[session_hash]) {
		db_query("SELECT * FROM session WHERE session_hash='$GLOBALS[session_hash]'");
		$G_SESSION = db_fetch_array();

		// does hash exist?
		if ($G_SESSION[session_hash]) {
			if ($G_SESSION[ip_addr] == $GLOBALS[REMOTE_ADDR]) {
				$id_is_good = 1;
			} // else ip is different
		} // else hash was not in database
	} // else (hash does not exist) or (session hash is bad)
	
	// Must create new session id... (need expired dialog?)
	if (! $id_is_good) {
		session_set_new();	
	}

	session_setglobals();	
	return $GLOBALS[session_hash];
}

?>
