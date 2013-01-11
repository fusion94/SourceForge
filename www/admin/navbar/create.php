<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: create.php,v 1.2 2000/01/13 18:36:34 precision Exp $

      ////////////////////////////////////
      //                                //  
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                //
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     //
      //                                //
      //////////////////////////////////// 

// include common configuration, database connection
//
$root = "../..";
require("$root/my_lib.php3");
db_open_connection();
include("common.inc");

// test form variables, name uniqueness, database insertion
//
if (empty($f_strName) || empty($f_strDesc)) {
	$strError = "Form variables not set.  Please resubmit with a name and description.";
} else if (@mysql_result(@mysql_query("SELECT COUNT(*) FROM menus WHERE name='$f_strName'", $GLOBALS[DB]), 0, 0) > 0) {
	$strError = "The menu name you have chosen is not unique.  Plese resubmit with a new menu name.";
} else if (!@mysql_query("INSERT INTO menus VALUES (0, '$f_strName', '$f_strDesc', 0, 0, 0, 1)", $GLOBALS[DB])) {
	$strError = "Database query failed;<br>" . mysql_errno() . ": " . mysql_error() . "<br>Please contact the system administrator.";
}

// output error page or redirect
//
if (empty($strError)) {
	header("Location: index.php3");
} else {
	include("$root/head.php3");
	ErrorPage($strError);
	include("$root/post.php3");
}

?>
