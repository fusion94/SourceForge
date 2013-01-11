<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: session.php,v 1.88 2000/04/14 13:50:14 dtype Exp $
//

// ############################function session_checkip()
// ## makes sure that IP is legal and from same subnet

function session_login_valid($form_loginname,$form_pw,$allowpending=0)  {
	global $session_hash;

	if (!$form_loginname || !$form_pw) {
		error_set_true();
		error_set_string('Missing Password Or User Name');
		return false;
	}

	//get the user from the database using user_id and password
	$res = db_query("SELECT user_id,status FROM user WHERE "
		. "user_name='$form_loginname' "
		. "AND user_pw='" . md5($form_pw) . "'");
	if (!$res || db_numrows($res) < 1) {
		//invalid password or user_name
		error_set_true();
		error_set_string('Invalid Password Or User Name');
		return false;
	}

	// check status of this user
	$usr = db_fetch_array($res);

	// if allowpending (for verify.php) then allow
	if ($allowpending && ($usr['status'] == 'P')) {
		1;
	} else {

		if ($usr['status'] == 'S') { 
			//acount suspended
			error_set_true();
			error_set_string('Account Suspended');
			return false;
		}
		if ($usr['status'] == 'P') { 
			//account pending
			error_set_true();
			error_set_string('Account Pending');
			return false;
		} 
		if ($usr['status'] == 'D') { 
			//account deleted
			error_set_true();
			error_set_string('Account Deleted');
			return false;
		}
		if ($usr['status'] != 'A') {
			//unacceptable account flag
			error_set_true();
			error_set_string('Account Not Active');
			return false;
		}
	}
	//create a new session
	session_set_new();

	// if we got this far, the name/pw must be ok
	db_query("UPDATE session SET user_id=" . db_result($res,0,'user_id')
		. " WHERE session_hash='$session_hash'");

	return true;
}

function session_checkip($oldip,$newip) {
	$eoldip = explode(".",$oldip);
	$enewip = explode(".",$newip);
	
	// ## require same class b subnet
	if (($eoldip[0]!=$enewip[0])||($eoldip[1]!=$enewip[1])) {
		return 0;
	} else {
		return 1;
	}
}

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
		. "'" . $GLOBALS['session_hash'] . "',"
		. time() . ","
		. user_getid() . ","
		. "'" . $cat . "',"
		. "'" . $desc . "')");
}

// ######################### function session_require

function session_require($req) {
	if (user_ismember(1,'A')) { return 1; }

	if ($req['group']) {
		$query = "SELECT user_id FROM user_group WHERE user_id=" . user_getid()
			. " AND group_id=$req[group]";
		if ($req['admin_flags']) {
		$query .= " AND admin_flags = '$req[admin_flags]'";	
		}
 
		if ((db_numrows(db_query($query)) < 1) || !$req['group']) {
			exit_error("Insufficient Group Access","You do not have permission to "
				. "view this page.");
		}
	}
	elseif ($req['user']) {
		if (user_getid() != $req['user']) {	
			exit_error("Insufficient User Access","You do not have permission to "
				. "view this page.");
		}
	}
	elseif ($req['isloggedin']) {
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

	if ($G_SESSION['user_id']) {
		db_query("SELECT user_id,user_name FROM user WHERE user_id='$G_SESSION[user_id]'");
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
		$GLOBALS['session_hash'] = md5($pre_hash);
	} while (db_numrows(db_query("SELECT session_hash FROM session WHERE session_hash='$GLOBALS[session_hash]'")) > 0);
		
	// set session cookie
	session_cookie("session_hash",$GLOBALS['session_hash']);

	// make new session entries into db
	db_query("INSERT INTO session (session_hash, ip_addr, time) VALUES "
		. "('$GLOBALS[session_hash]','$GLOBALS[REMOTE_ADDR]'," . time() . ")");

	// set global
	$G_SESSION = db_fetch_array(db_query("SELECT * FROM session WHERE session_hash='$GLOBALS[session_hash]'"));

	session_setglobals();
}

// ###################### function session_set()

function session_set() {
	global $G_SESSION;

	unset($G_SESSION);

	// assume bad session_hash and session. If all checks work, then allow
	// otherwise make new session
	$id_is_good = 0;
	
	// here also check for good hash, set if new session is needed
	if ($GLOBALS['session_hash']) {
		db_query("SELECT * FROM session WHERE session_hash='$GLOBALS[session_hash]'");
		$G_SESSION = db_fetch_array();

		// does hash exist?
		if ($G_SESSION['session_hash']) {
			if (session_checkip($G_SESSION['ip_addr'],$GLOBALS['REMOTE_ADDR'])) {
				$id_is_good = 1;
			} // else ip is different
		} // else hash was not in database
	} // else (hash does not exist) or (session hash is bad)
	
	// Must create new session id... (need expired dialog?)
	//if (! $id_is_good) {
	//	session_set_new();
	//}

	session_setglobals();
	//return $GLOBALS['session_hash'];
}

?>
