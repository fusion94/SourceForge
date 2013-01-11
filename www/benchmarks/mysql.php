<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mysql.php,v 1.3 2000/07/06 17:35:10 tperdue Exp $
//
// /etc/local.inc includes the machine specific database connect info

function db_connect() {
	global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$conn;
	$conn = @mysql_pconnect($sys_dbhost,$sys_dbuser,$sys_dbpasswd);
	#return $conn;
}

function db_query($qstring,$print=0) {
//	global $QUERY_COUNT;
//	$QUERY_COUNT++;
	if ($print) print "<br>Query is: $qstring<br>";
//	if ($GLOBALS[IS_DEBUG]) $GLOBALS[G_DEBUGQUERY] .= $qstring . "<BR>\n";
	global $sys_dbname;
	$GLOBALS['db_qhandle'] = @mysql($sys_dbname,$qstring);
	return $GLOBALS['db_qhandle'];
}

function db_numrows($qhandle) {
	// return only if qhandle exists, otherwise 0
	if ($qhandle) {
		return @mysql_numrows($qhandle);
	} else {
		return 0;
	}
}

function db_result($qhandle,$row,$field) {
	return @mysql_result($qhandle,$row,$field);
}

function db_numfields($lhandle) {
	return @mysql_numfields($lhandle);
}

function db_fieldname($lhandle,$fnumber) {
           return @mysql_fieldname($lhandle,$fnumber);
}

function db_affected_rows($qhandle) {
	return @mysql_affected_rows();
}
	
function db_fetch_array($qhandle = 0) {
	if ($qhandle) {
		return @mysql_fetch_array($qhandle);
	} else {
		if ($GLOBALS['db_qhandle']) {
			return @mysql_fetch_array($GLOBALS['db_qhandle']);
		} else {
			return (array());
		}
	}
}
	
function db_error() {
	return @mysql_error();
}

?>
