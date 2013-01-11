<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: delete.php,v 1.2 2000/01/13 18:36:34 precision Exp $

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

// test form variables
//
if ($f_strAction == "Abort") {
	header("Location: index.php3");
} else if ($f_strAction == "Delete") {
	@mysql_query("UPDATE buttons SET active=0 WHERE mid=$f_mid", $GLOBALS[DB]);
	@mysql_query("UPDATE menus SET active=0 WHERE mid=$f_mid", $GLOBALS[DB]);
	header("Location: index.php3");
} else {
	if (empty($f_mid) || !($dbResult=mysql_query("SELECT name, description FROM menus WHERE mid=$f_mid AND active=1", $GLOBALS[DB]))) {
		include("$root/head.php3");
		ErrorPage("Menu ID variable not set.  Please resubmit with a valid menu ID.");
		include("$root/post.php3");
	} else {

		$dbRow = mysql_fetch_array($dbResult);
		print("<form action='delete.php3' method='POST'>\n");
		printf("<input type='hidden' name='f_mid' value='%s'>\n", $f_mid);
		print("<center><img src='http://www.varesearch.com/images/logo.jpeg'></center>\n");
		print("<center><h4>Click below to delete the following menu:</center></h4>");
		print("<table cellpadding=2 cellspacing=0 border=1 align='center'>\n");
		print("<tr bgcolor='#314a9c'><th><font color='#FFFFFF'>Menu Name</font></th><th><font color='#FFFFFF'>Description</font></th></tr>\n");
		printf("<tr bgcolor='#DDDDDD'>", $strColor);
		printf("<td>%s</td>\n", $dbRow["name"]);
		printf("<td>%s</td>\n", $dbRow["description"]);
		printf("</td></tr>\n");
		print("</table>\n");
		print("<br><center><input type='submit' name='f_strAction' value='Delete'><input type='submit' name='f_strAction' value='Abort'></center>");
		print("</form>\n");

		include("$root/post.php3");
	}
}

?>

