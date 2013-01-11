<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: edit_menu.php,v 1.2 2000/01/13 18:36:34 precision Exp $

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

if (empty($f_mid)) {
	include("$root/head.php3");
	ErrorPage("Menu ID variable not set.  Please resubmit with a valid menu ID.");
	include("$root/post.php3");
	exit();
} else {
	if ($f_strAction == 'Add') {
		if (empty($f_nType) || empty($f_strText) || empty($f_strURL) || empty($f_strALT) || empty($f_nSequence)) {
			include("$root/head.php3");
			ErrorPage("One or more required form variables was left blank.  Please resubmit with all fields filled in.");
			include("$root/post.php3");
			exit();
		} else {
			// clean up variables
			//
			$strCleanText = addslashes($f_strText);
			$strCleanALT = addslashes($f_strALT);
			$strCleanURL = addslashes($f_strURL);

			// generate button name 
			//   form: MxTxSxxxxxx where M tags MenuID, T tags TypeID, S tags text string
			//   note: the eregi... business cleans up non-alphanumeric text for easy disk and db access (no slashes, metachars, etc.)
			//
			$strButtonFileName = "M" . $f_mid . "T" . $f_nType . "S" . eregi_replace("%..", "_", rawurlencode($f_strText)) . ".gif";

			// create button if nonexistant
			//
			$nDupeButtonCount = mysql_result(mysql_query("SELECT COUNT(*) FROM buttons WHERE tid=$f_nType AND text='$strCleanText'", $GLOBALS[DB]), 0, 0);
			if ($nDupeButtonCount == 0) {
				// find script name
				//
				$strScriptName = mysql_result(mysql_query("SELECT file FROM buttontypes WHERE tid=$f_nType", $GLOBALS[DB]), 0, 0);
				include($gstrPathScripts . $strScriptName);
				CreateGIF($f_strText, $gstrPathImages . $strButtonFileName);
			}

			// test for duplicate sequence in this menuset
			//
			$nDupeSeqCount = mysql_result(mysql_query("SELECT COUNT(*) FROM buttons WHERE mid=$f_mid AND sequence=$f_nSequence", $GLOBALS[DB]), 0, 0);
			if ($nDupeSeqCount > 0) {
				// increment button counts after inserted button
				//
				mysql_query("UPDATE buttons SET sequence = sequence + 1 WHERE sequence >= $f_nSequence AND mid=$f_mid", $GLOBALS[DB]);
			}

			// insert the button
			//
			mysql_query("INSERT INTO buttons VALUES (0, $f_mid, $f_nType, '$strCleanText', '$strCleanURL', '$strCleanALT', $f_nSequence, 0, 0, '$strButtonFileName', '$f_strSecurityIP' ,'$f_strSecurityUser', 1, '$f_strMenuSubset')", $GLOBALS[DB]);
		}
	} else if (($f_strAction == 'Delete') && !empty($f_bid)) {
		// restore sequence numbers, set active flag to 0
		//
		$nSequence = mysql_result(mysql_query("SELECT sequence FROM buttons WHERE bid=$f_bid", $GLOBALS[DB]), 0, 0);
		mysql_query("UPDATE buttons SET sequence = sequence - 1 WHERE mid = $f_mid AND sequence >= $nSequence", $GLOBALS[DB]);
		mysql_query("UPDATE buttons SET active=0 WHERE bid=$f_bid", $GLOBALS[DB]);
	}

	// query for button list
	//
	$dbResultButtonList = mysql_query("SELECT * FROM buttons WHERE mid=$f_mid AND active=1 ORDER BY sequence", $GLOBALS[DB]);

	// query for list of button types -> associative array
	//
	$dbResultTypeList = mysql_query("SELECT * FROM buttontypes ORDER BY name", $GLOBALS[DB]);
	while($dbRow = mysql_fetch_array($dbResultTypeList)) {
		$aButtonTypes[$dbRow["tid"]] = $dbRow["name"];
	}

	// query for menu name
	//
	$strMenuName = mysql_result(mysql_query("SELECT CONCAT(name, ' - ', description) FROM menus WHERE mid = $f_mid", $GLOBALS[DB]), 0, 0);

	include("$root/head.php3");

	// page title and header
	//
	printf("<center><h3>Menu: %s</h3></center>\n", $strMenuName);
	print("<center><h4>Delete Buttons</h4></center>\n");
	print("<table cellpadding=2 cellspacing=0 border=1 align='center'>\n");
	print("<tr bgcolor='#314a9c'><th><font color='#FFFFFF'>Button</font></th><th><font color='#FFFFFF'>#</font></th><th><font color='#FFFFFF'>Type</font></th><th><font color='#FFFFFF'>Text</font></th><th><font color='#FFFFFF'>ALT Tag</font></th><th><font color='#FFFFFF'>URL</font></th><th><font color='#FFFFFF'>Subset Name</font></th><th><font color='#FFFFFF'>User Security</font></th><th><font color='#FFFFFF'>IP Security</font></th><th><font color='#FFFFFF'>Actions</font></th></tr>\n");
	while($dbRow = mysql_fetch_array($dbResultButtonList)) {
		(($nCount++ % 2) == 0) ? $strColor = "#DDDDDD" : $strColor = "#FFFFFF";
		printf("<tr bgcolor='%s'>", $strColor);
		printf("<td bgcolor='#000000'><a href='%s'><img border=0 src='%s'></a></td>\n", $dbRow["url"], $gstrPathImageURL . addslashes($dbRow["file"]));
		printf("<td>&nbsp;%s</td>\n", $dbRow["sequence"]);
		printf("<td>&nbsp;%s</td>\n", $aButtonTypes[$dbRow["tid"]]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["text"]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["alt"]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["url"]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["subset"]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["security_user"]);
		printf("<td>&nbsp;%s</td>\n", $dbRow["security_ip"]);
		printf("<td><a href='edit_button.php3?f_bid=%s&f_mid=%s'>Edit</a> - <a href='edit_menu.php3?f_strAction=Delete&f_bid=%s&f_mid=%s'>Delete</a></td>\n", $dbRow["bid"], $f_mid, $dbRow["bid"], $f_mid);
		printf("</td></tr>\n");
	}
	print("</table>\n");

	// add button section
	//
	print("<center><h4>Add A Button</h4></center>\n");
	print("<form action='edit_menu.php3' method='POST'>\n");
	printf("<input type='hidden' name='f_mid' value='%s'>", $f_mid);
	print("<table cellpadding=2 cellspacing=0 border=0 align='center'>\n");
	print("<tr><td><b>Insert After Button:</b></td><td><select name='f_nSequence'>");
	print("<option value='1'>[Button 0]\n");
	for ($i = 0; $i < mysql_numrows($dbResultButtonList); $i++) {
		printf("<option value='%s'>%s\n", $i + 2, mysql_result($dbResultButtonList, $i, "text"));
	}
	print("</select></td></tr>");
	print("<tr><td><b>Button Type:</b></td><td><select name='f_nType'>");
	while ( list($key, $val) = each($aButtonTypes) ) {
		printf("<option value='%s'>%s", $key, $val);
	}
	print("</select></td></tr>");
	print("<tr><td><b>Button Text:</b></td><td><input type='text' name='f_strText' size=20 maxlen=32></td></tr>");
	print("<tr><td><b>ALT Tag:</b></td><td><input type='text' name='f_strALT' size=20 maxlen=32></td></tr>");
	print("<tr><td><b>URL:</b></td><td><input type='text' name='f_strURL' size=20 maxlen=128></td></tr>");
	print("<tr><td><b>Button Subset Name:</b></td><td><input type='text' name='f_strMenuSubset' size=20 maxlen=255></td></tr>");
	print("<tr><td><b>User-Level Security:</b></td><td><input type='text' name='f_strSecurityUser' size=20 maxlen=32></td></tr>");
	print("<tr><td><b>IP-Level Security:</b></td><td><input type='text' name='f_strSecurityIP' size=20 maxlen=255></td></tr>");
	print("<tr><td align='center' colspan='2'><input type='submit' value='Add' name='f_strAction'><input type='reset' value='Clear'></td></tr>");
	print("</table>\n");
	print("</form>");

	print("<p><b>Note:</b> User and IP-level security parameters must be correctly formatted as follows:<ul><li>The user-level security field may be filled with a character string, restricting access to members of groups represented by each character.  An example user-level security field is 'STA' where access is granted to members of the 'S'ales, 'T'echnical Support, and 'A'dministrative groups.<li>IP-level security restricts access by IP ranges specified in a comma-seperated list where '*' is the wildcard character.  For example, '128.253.*,10.*' would only allow access from the listed class B and A ranges.  Note that '128.253.1*' is not valid, only full-class wildcard entries are allowed.</ul></p>");

	print("<center><a href='index.php3'>Return to Menu List</a></center>\n");

	include("$root/post.php3");
}

?>



















