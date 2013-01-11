<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: pm_nav.php,v 1.9 2000/01/13 18:36:36 precision Exp $

html_tabs('pm',$group_id);

echo "<P><B><A HREF=\"/pm/?group_id=$group_id\">Main</A>".
	" | <A HREF=\"/pm/admin/?group_id=$group_id\">Admin</A>";

if ($group_project_id) {
	echo " | <A HREF=\"/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=open\">Open Tasks</A>";
	if (user_isloggedin()) {
		echo " | <A HREF=\"/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=my\">My Tasks</A>";
	}
	echo " | <A HREF=\"/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=browse&set=closed\">Closed Tasks</A>";
	if (user_isloggedin()) {
		echo " | <A HREF=\"/pm/task.php?group_id=$group_id&group_project_id=$group_project_id&func=addtask\">Add Task</A>";
	}
}

echo "</B>";
?>
