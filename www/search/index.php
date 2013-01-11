<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.34 2000/01/13 18:36:36 precision Exp $

require ('pre.php');

site_header(array(title=>"Search"));

echo "<P><CENTER>";

menu_show_search_box();

/*
	Force them to enter at least three characters
*/
if ($words && (strlen($words) < 3)) {
	echo "<H2>Search must be at least three characters</H2>";
	site_footer(array());
	exit;
}

if (!$words) {
	echo "<BR>Enter Your Search Words Above</CENTER><P>";
	site_footer(array());
	exit;
}

$words=trim(ltrim($words));

if (!$offset || $offset < 0) {
	$offset=0;
}

if ($type_of_search == "soft") {
	/*
		If multiple words, separate them and put LIKE in between
	*/
	$array=explode(" ",$words);
	$words1=implode($array,"%' OR group_name LIKE '%");
	$words2=implode($array,"%' OR short_description LIKE '%");

	/*
		Query to find software
	*/
	$sql = "SELECT group_name,group_id,short_description ".
		"FROM groups ".
		"WHERE status IN ('A','H') AND public=1 AND (group_name LIKE '%$words1%' OR short_description LIKE '%$words2%') LIMIT $offset,25";
	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		echo "<H2>No matches found for $words</H2>";
		echo db_error();
	} else {

		if (db_numrows($result) > 25) {
			$rows=25;
		}

		echo "<H3>Search results for $words</H3>";
		//echo "\n<table cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" BORDER=\"0\"><tr><td align=CENTER bgcolor=\"#666699\">".
		echo "\n<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"0\" BGCOLOR=\"#FFFFFF\" BORDER=\"0\">\n";
		echo "\n<TR BGCOLOR=\"#666699\"><TD WIDTH=\"25%\"><FONT COLOR=#FFFFFF><B>Group Name</TD>".
			"\n<TD><FONT COLOR=#FFFFFF><B>Description</TD></TR>\n";
 
		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 != 0) {
				$row_color=" BGCOLOR=\"#EEEEF8\"";
			} else {
				$row_color=" BGCOLOR=\"#FFFFFF\"";
			}
			/*
				Build row
			*/
			echo "\n<TR$row_color><TD><A HREF=\"/project/?group_id=".db_result($result, $i, 'group_id')."\">".
				"<IMG SRC=\"/images/msg.gif\" BORDER=0 HEIGHT=12 WIDTH=10> ".db_result($result, $i, 'group_name')."</A></TD>".
				"<TD>".db_result($result,$i,'short_description')."</TD></TR>";
		}
		/*
			This code puts the nice next/prev.
		*/
		echo "<TR BGCOLOR=\"#EEEEEE\"><TD>";
		if ($offset != 0) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"javascript:history.back()\"><B><IMG SRC=\"/images/t2.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TD><TD>";

		if (db_numrows($result) > $rows) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"/search/?type_of_search=$type_of_search&words=".urlencode($words)."&offset=".($offset+25)."\"><B>Next Messages <IMG SRC=\"/images/t.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TABLE>";//</TD></TR></TABLE>";
	}

} else if ($type_of_search == "people") {
	/*
		If multiple words, separate them and put LIKE in between
	*/
	$array=explode(" ",$words);
	$words1=implode($array,"%' OR user_name LIKE '%");
	$words2=implode($array,"%' OR realname LIKE '%");

	/*
		Query to find users
	*/
	$sql="SELECT user_name,user_id,realname ".
		"FROM user ".
		"WHERE (user_name LIKE '%$words1%' OR realname LIKE '%$words2%') AND (status='A') ORDER BY user_name LIMIT $offset,25";
	
	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		echo "<H2>No matches found for $words</H2>";
		echo db_error();
	} else {

		if (db_numrows($result) > 25) {
			$rows=25;
		}

		echo "<H3>Search results for $words</H3>";
		//echo "\n<table cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" BORDER=\"0\"><tr><td align=CENTER bgcolor=\"#666699\">".
		echo "\n<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"0\" BGCOLOR=\"#FFFFFF\" BORDER=\"0\">\n";
		echo "\n<TR BGCOLOR=\"#666699\"><TD WIDTH=\"50%\"><FONT COLOR=#FFFFFF><B>User Name</TD>".
			"\n<TD><FONT COLOR=#FFFFFF><B>Real Name</TD></TR>\n";
 
		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 != 0) {
				$row_color=" BGCOLOR=\"#EEEEF8\"";
			} else {
				$row_color=" BGCOLOR=\"#FFFFFF\"";
			}
			/*
				Build row
			*/
			echo "\n<TR$row_color><TD><A HREF=\"/developer/?form_dev=".db_result($result, $i, 'user_id')."\">".
				"<IMG SRC=\"/images/msg.gif\" BORDER=0 HEIGHT=12 WIDTH=10> ".db_result($result, $i, 'user_name')."</A></TD>".
				"<TD>".db_result($result,$i,'realname')."</TD></TR>";
		}
		/*
			This code puts the nice next/prev.
		*/
		echo "<TR BGCOLOR=\"#EEEEEE\"><TD>";
		if ($offset != 0) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"javascript:history.back()\"><B><IMG SRC=\"/images/t2.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TD><TD>";

		if (db_numrows($result) > $rows) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"/search/?type_of_search=$type_of_search&words=".urlencode($words)."&offset=".($offset+25)."\"><B>Next Messages <IMG SRC=\"/images/t.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TABLE>";//</TD></TR></TABLE>";
	}
} else if ($type_of_search == 'forums') {
	$array=explode(" ",$words);
	$words1=implode($array,"%' OR forum.body LIKE '%");
	$words2=implode($array,"%' OR forum.subject LIKE '%");

	$sql="SELECT forum.msg_id,forum.subject,forum.date,user.user_name ".
		"FROM forum,user ".
		"WHERE user.user_id=forum.posted_by AND (forum.body LIKE '%$words1%' ".
		"OR forum.subject LIKE '%$words2%') AND forum.group_forum_id='$forum_id' ".
		"GROUP BY msg_id,subject,date,user_name LIMIT $offset,26";

//      echo $sql;

	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		echo "<H2>No matches found for $words</H2>";
	} else {
                if (db_numrows($result) > 25) {
                        $rows=25;
                }

		echo "<H3>Search results for $words</H3>";
		//echo "\n<table cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" BORDER=\"0\"><tr><td align=CENTER bgcolor=\"#666699\">".
		echo "\n<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"0\" BGCOLOR=\"#FFFFFF\" BORDER=\"0\">\n";
		echo "\n<TR BGCOLOR=\"#666699\"><TD WIDTH=\"25%\"><FONT COLOR=#FFFFFF><B>Thread/Subject</TD>".
			"<TD><FONT COLOR=#FFFFFF><B>Author</TD>".
			"\n<TD><FONT COLOR=#FFFFFF><B>Date/Time</TD></TR>\n";

		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 != 0) {
				$row_color=" BGCOLOR=\"#EEEEF8\"";
			} else {
				$row_color=" BGCOLOR=\"#FFFFFF\"";
			}
			/*
				Build row
			*/
			echo "\n<TR$row_color><TD><A HREF=\"/forum/message.php?msg_id=".
				db_result($result, $i, "msg_id")."\"><IMG SRC=\"/images/msg.gif\" BORDER=0 HEIGHT=12 WIDTH=10> ".
				db_result($result, $i, "subject")."</A></TD>".
				"<TD>".db_result($result, $i, "user_name")."</TD>".
				"<TD>".date($sys_datefmt,db_result($result,$i,"date"))."</TD></TR>";
		}
		/*
			This code puts the nice next/prev.
		*/
		echo "<TR><TD BGCOLOR=#EEEEEE>";
		if ($offset != 0) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"javascript:history.back()\"><B>".
				"<IMG SRC=\"/images/t2.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TD><TD BGCOLOR=\"#EEEEEE\">&nbsp;</TD><TD BGCOLOR=\"#EEEEEE\">";
		if (db_numrows($result) > $rows) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"/search/?type_of_search=$type_of_search&words=".urlencode($words).
				"&offset=".($offset+25)."&forum_id=".$forum_id.
				"\"><B>Next Messages <IMG SRC=\"/images/t.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A></B></FONT>";
		} else {
			echo "&nbsp;";
		}
		//

		echo "</TABLE>";//</TD></TR></TABLE>";

	}
} else if ($type_of_search == 'bugs') {
	$array=explode(" ",$words);
	$words1=implode($array,"%' OR bug.details LIKE '%");
	$words2=implode($array,"%' OR bug.summary LIKE '%");

	$sql="SELECT bug.bug_id,bug.summary,bug.date,user.user_name ".
		"FROM bug,user ".
		"WHERE user.user_id=bug.submitted_by AND (bug.details LIKE '%$words1%' ".
		"OR bug.summary LIKE '%$words2%') AND bug.group_id='$group_id' ".
		"GROUP BY bug_id,summary,date,user_name LIMIT $offset,26";

//      echo $sql;

	$result=db_query($sql);
	$rows=db_numrows($result);

	if (!$result || $rows < 1) {
		echo "<H2>No matches found for $words</H2>";
		echo db_error();
	} else {
                if (db_numrows($result) > 25) {
                        $rows=25;
                }

		echo "<H3>Search results for $words</H3>";
		//echo "\n<table cellspacing=\"0\" cellpadding=\"2\" width=\"100%\" BORDER=\"0\"><tr><td align=CENTER bgcolor=\"#666699\">".
		echo "\n<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"0\" BGCOLOR=\"#FFFFFF\" BORDER=\"0\">\n";
		echo "\n<TR BGCOLOR=\"#666699\"><TD WIDTH=\"25%\"><FONT COLOR=#FFFFFF><B>Bug Summary</TD>".
			"<TD><FONT COLOR=#FFFFFF><B>Submitted By</TD>".
			"\n<TD><FONT COLOR=#FFFFFF><B>Date/Time</TD></TR>\n";

		for ($i=0; $i<$rows; $i++) {
			if ($i % 2 != 0) {
				$row_color=" BGCOLOR=\"#EEEEF8\"";
			} else {
				$row_color=" BGCOLOR=\"#FFFFFF\"";
			}
			/*
				Build row
			*/
			echo "\n<TR$row_color><TD><A HREF=\"/bugs/?group_id=$group_id&func=detailbug&bug_id=".
				db_result($result, $i, "bug_id")."\"><IMG SRC=\"/images/msg.gif\" BORDER=0 HEIGHT=12 WIDTH=10> ".
				db_result($result, $i, "summary")."</A></TD>".
				"<TD>".db_result($result, $i, "user_name")."</TD>".
				"<TD>".date($sys_datefmt,db_result($result,$i,"date"))."</TD></TR>";
		}
		/*
			This code puts the nice next/prev.
		*/
		echo "<TR BGCOLOR=\"#EEEEEE\"><TD>";
		if ($offset != 0) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"javascript:history.back()\">".
				"<B><IMG SRC=\"/images/t2.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE> Previous Messages</A></B></FONT>";
		} else {
			echo "&nbsp;";
		}

		echo "</TD><TD>&nbsp;</TD><TD>";
		if (db_numrows($result) > $rows) {
			echo "<FONT face=\"Arial, Helvetica\" SIZE=3 STYLE=\"text-decoration: none\"><B>";
			echo "<A HREF=\"/search/?type_of_search=$type_of_search&words=".urlencode($words)."&offset=".
				($offset+25)."&group_id=".$group_id.
				"\"><B>Next Messages <IMG SRC=\"/images/t.gif\" HEIGHT=15 WIDTH=15 BORDER=0 ALIGN=MIDDLE></A></B></FONT>";
		} else {
			echo "&nbsp;";
		}
		//

		echo "</TABLE>";//</TD></TR></TABLE>";

	}
} else {

	echo "<H1>Invalid Search - ERROR!!!!</H1>";

}

/*
if ($form_searchtype == "title") {
	$query .= "(group_name ";
	if ($form_regexp) { $query .= "REGEXP '"; } else { $query .= "LIKE '%"; }
	$query .= $form_findquery;
	if ($form_regexp) { $query .= "'"; } else { $query .= "%'"; }
	
	$query .= " OR unix_group_name ";
	if ($form_regexp) { $query .= "REGEXP '"; } else { $query .= "LIKE '%"; }
	$query .= $form_findquery;
	if ($form_regexp) { $query .= "'"; } else { $query .= "%'"; }

	$query .= ") ORDER BY group_name";
}

$res_search = db_query($query);

while ($row_search = db_fetch_array($res_search)) {
	print "<P>";
	html_image("blackdot.gif",array());
	print " <a href=\"/project/?group_id=$row_search[group_id]\">$row_search[group_name]</a> ";
	if ($row_search[short_description]) {
		print "- $row_search[short_description]";
	}
}
*/

site_footer(array());
site_cleanup(array());
?>
