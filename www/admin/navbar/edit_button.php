<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: edit_button.php,v 1.2 2000/01/13 18:36:34 precision Exp $

      ////////////////////////////////////
      //                                //  
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                //
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     //
      //                                //
      //////////////////////////////////// 

//
// NOTE: button creation and deletion should be wrapped into functions.  There is a large block
// of code duplication between this file and edit_menu.php3 that needs resolution.
// - jb
//

// include common configuration, database connection
//
$root = "../..";
require("$root/my_lib.php3");
db_open_connection();
include("common.inc");

if (empty($f_bid) || empty($f_mid)) {
	include("$root/head.php3");
	ErrorPage("The button and menu IDs were not set.  Please check referring URL.");
	include("$root/post.php3");
	exit();
} else if ($f_strAction == 'Edit') {
	if (empty($f_nType) || empty($f_strText) || empty($f_strURL) || empty($f_strALT) || empty($f_nSequence)) {
		include("$root/head.php3");
		ErrorPage("One or more required form variables was left blank.  Please resubmit with all fields filled in.");
		include("$root/post.php3");
		exit();
	} else {
		// delete old button; restore sequence numbers, set active flag to 0
		//
		$nSequence = mysql_result(mysql_query("SELECT sequence FROM buttons WHERE bid=$f_bid", $GLOBALS[DB]), 0, 0);
		mysql_query("UPDATE buttons SET sequence = sequence - 1 WHERE mid = $f_mid AND sequence >= $nSequence", $GLOBALS[DB]);
		mysql_query("UPDATE buttons SET active=0 WHERE bid=$f_bid", $GLOBALS[DB]);

		// create new button
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

		// bounce to index.php3
		//
		header("Location: edit_menu.php3?f_mid=$f_mid");
	}
} else {
	// query for button info
	//
	$dbResultButtonInfo = mysql_query("SELECT * FROM buttons WHERE bid=$f_bid", $GLOBALS[DB]);

	// query for button list
	//
	$dbResultButtonList = mysql_query("SELECT * FROM buttons WHERE mid=$f_mid AND active=1 ORDER BY sequence", $GLOBALS[DB]);

	// query for list of button types -> associative array
	//
	$dbResultTypeList = mysql_query("SELECT * FROM buttontypes ORDER BY name", $GLOBALS[DB]);
	while($dbRow = mysql_fetch_array($dbResultTypeList)) {
		$aButtonTypes[$dbRow["tid"]] = $dbRow["name"];
	}

	include("$root/head.php3");

	print("<center><h4>Edit Button</h4></center>\n");
	print("<form action='edit_button.php3' method='POST'>\n");
	printf("<input type='hidden' name='f_bid' value='%s'>\n", $f_bid);
	printf("<input type='hidden' name='f_mid' value='%s'>\n", $f_mid);
	print("<table cellpadding=2 cellspacing=0 border=0 align='center'>\n");
	print("<tr><td><b>Insert After Button #:</b></td><td><select name='f_nSequence'>");
	print("<option value='1'>[Button 0]\n");

	// increment by 2 until we pass the removed entry in the button list
	//
	$nFactor = 2;
	for ($i = 0; $i < mysql_numrows($dbResultButtonList); $i++) {
		if (mysql_result($dbResultButtonList, $i, "sequence") == mysql_result($dbResultButtonInfo, 0, "sequence")) {
			// now only increment by one; a button entry is now removed 
			//
			$nFactor = 1;
			continue;
		}
		printf("<option value='%s'", $i + $nFactor);
		if (mysql_result($dbResultButtonInfo, 0, "sequence") == $i + $nFactor) {
			print(" selected");
		}
		printf(">%s\n", mysql_result($dbResultButtonList, $i, "text"));
	}
	print("</select></td></tr>");
	print("<tr><td><b>Button Type:</b></td><td><select name='f_nType'>");
	while ( list($key, $val) = each($aButtonTypes) ) {
		printf("<option value='%s'>%s", $key, $val);
	}
	print("</select></td></tr>");
	printf("<tr><td><b>Button Text:</b></td><td><input type='text' name='f_strText' size=20 maxlen=32 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "text"));
	printf("<tr><td><b>ALT Tag:</b></td><td><input type='text' name='f_strALT' size=20 maxlen=32 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "alt"));
	printf("<tr><td><b>URL:</b></td><td><input type='text' name='f_strURL' size=20 maxlen=128 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "url"));
	printf("<tr><td><b>Button Subset Name:</b></td><td><input type='text' name='f_strMenuSubset' size=20 maxlen=255 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "subset"));
	printf("<tr><td><b>User-Level Security:</b></td><td><input type='text' name='f_strSecurityUser' size=20 maxlen=32 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "security_user"));
	printf("<tr><td><b>IP-Level Security:</b></td><td><input type='text' name='f_strSecurityIP' size=20 maxlen=255 value='%s'></td></tr>", mysql_result($dbResultButtonInfo, 0, "security_ip"));
	print("<tr><td align='center' colspan='2'><input type='submit' value='Edit' name='f_strAction'><input type='reset' value='Clear'></td></tr>");
	print("</table>\n");
	print("</form>");

	print("<p><b>Note:</b> User and IP-level security parameters must be correctly formatted as follows:<ul><li>The user-level security field may be filled with a character string, restricting access to members of groups represented by each character.  An example user-level security field is 'STA' where access is granted to members of the 'S'ales, 'T'echnical Support, and 'A'dministrative groups.<li>IP-level security restricts access by IP ranges specified in a comma-seperated list where '*' is the wildcard character.  For example, '128.253.*,10.*' would only allow access from the listed class B and A ranges.  Note that '128.253.1*' is not valid, only full-class wildcard entries are allowed.</ul></p>");

	print("<center><a href='index.php3'>Return to Menu List</a></center>\n");

	include("$root/post.php3");
}

?>



















