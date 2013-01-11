<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: copy.php,v 1.2 2000/01/13 18:36:34 precision Exp $

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
if (empty($f_nSourceMID) || empty($f_strDestName) || empty($f_strDesc)) {
	$strError = "Form variables not set.  Please resubmit with a destination menu name and source menu name.";
} else if (@mysql_result(@mysql_query("SELECT COUNT(*) FROM menus WHERE name='$f_strDestName'", $GLOBALS[DB]), 0, 0) > 0) {
	$strError = "The destination menu name you have chosen is not unique.  Plese resubmit with a new menu name.";
} else {
	// create new menu row and determine new mid
	//
	mysql_query("INSERT INTO menus VALUES (0, '$f_strDestName', '$f_strDesc', 0, 0, 0, 1)", $GLOBALS[DB]);
	$nMID = mysql_result(mysql_query("SELECT mid FROM menus WHERE name = '$f_strDestName'", $GLOBALS[DB]), 0, 0);

	// copy all active rows from buttons belonging to this menu
	//
	$dbResult = mysql_query("SELECT * FROM buttons WHERE mid = $f_nSourceMID AND active = 1", $GLOBALS[DB]);
	while($dbRow = mysql_fetch_array($dbResult)) {
		$strQuery = "INSERT INTO buttons VALUES (0, $nMID, " . 
				$dbRow["tid"] . ", " .
				"'" . $dbRow["text"] . "', " . 
				"'" . $dbRow["url"] . "', " . 
				"'" . $dbRow["alt"] . "', " . 
				$dbRow["sequence"] . ", " .
				$dbRow["width"] . ", " .
				$dbRow["height"] . ", " .
				"'" . $dbRow["file"] . "', " . 
				"'" . $dbRow["security_ip"] . "', " . 
				"'" . $dbRow["security_user"] . "', " . 
				"'" . $dbRow["subset"] . "', " . 
				"1)";
		mysql_query($strQuery, $GLOBALS[DB]);
	}

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
