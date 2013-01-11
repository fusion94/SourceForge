<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: squal_pre.php,v 1.2 2000/04/17 16:59:54 tperdue Exp $

require('database.php');
require('session.php');
require('user.php');
//require('group.php');
require('error.php');
require('squal_exit.php');

$sys_datefmt = "m/d/y H:i";

// #### Connect to db

db_connect();

if (!$conn) {
	exit_error("Could Not Connect to Database",db_error());
}

require('logger.php');

// #### set session

session_set();

?>
