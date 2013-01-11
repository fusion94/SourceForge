<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: view_user.php,v 1.5 2000/01/13 18:36:34 precision Exp $

print("&nbsp;<br>\n");

$sql = "SELECT * from unix_user ORDER BY id";
$result = db_query($sql);

if ($result && db_numrows($result) > 0) {

	$rows = db_numrows($result);

	print("<TABLE WIDTH=\"99%\">\n");
	print("<TR><A HREF=\"$PHP_SELF?action=add\">Click Here to Add a User</A></TR>\n");
	print("<TR><TD BGCOLOR=\"#999999\"><B>UID:</TD><TD BGCOLOR=\"#999999\"><B>Status:</TD><TD BGCOLOR=\"#999999\"><B>Username:</TD><TD BGCOLOR=\"#999999\"><B>Web UID: &nbsp;&nbsp;&nbsp;&nbsp; </TD><TD BGCOLOR=\"#999999\"><B>Shell: &nbsp;&nbsp; </TD><TD BGCOLOR=\"#999999\"><B>Added By:</TD><TD BGCOLOR=\"#999999\"><B>Date Added:</TD></TR>\n");

	for ($i = 0; $i < $rows; $i++) {

		$uid = db_result($result,$i,"id");

		print("<TR><TD><TD>$uid</TD><TD>");
	
		$status = db_result($result,$i,"status");
		
		if ($status == 1) {
			print("Active");
		} elseif ($status == 2) {
			print("Deleted");
		} elseif ($status == 3) {
			print("Suspended");
		} else {
			print("Unknown");
		}
		print("</TD><TD><A HREF=\"$PHP_SELF?action=details&uid=$uid\">". db_result($result,$i,"username"). "</A>".
		"</TD><TD>". db_result($result,$i,"user_id"). 
		"</TD><TD>". db_result($result,$i,"shell").
		"</TD><TD>". db_result($result,$i,"added_by").
		"</TD><TD>". date( "m/d/Y", db_result($result,$i,"datetime")).
		"</TD></TR>\n");
	}

	print("</TABLE>\n");
	
} else {
	print("<h1>No Users Found</h1><br>\n");
	print("<a href=$PHP_SELF?action=add>Click here</a> to add a user\n");
}

?>

</table>
