<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.42 2000/01/13 18:36:35 precision Exp $

require ('pre.php');
require ('vote_function.php');

if (user_isloggedin()) {

	site_header(array('title'=>'My Personal Page'));
	?>

	<H3>Personal Page for: <?php print user_getname(); ?></H3>
	<P>
	Your personal page contains lists of bugs and tasks that 
	you are assigned, plus a list of groups that you are a member of.
	<P>
	<TABLE width="100%" border="0">
	<TR><TD VALIGN="TOP" WIDTH="50%">
	<?php

	/*
		Bugs assigned to or submitted by this person
	*/
	$last_group=0;
	html_box1_top('My Bugs');
	$sql="SELECT group_id,bug_id,priority,summary ".
		"FROM bug ".
		"WHERE status_id <> '3' ".
		"AND (assigned_to='".user_getid()."' ".
		"OR submitted_by='".user_getid()."') ORDER BY group_id ASC LIMIT 100";

	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '
			<B>No Open Bugs are assigned to you or were submitted by you</B>';
	} else {
		for ($i=0; $i<$rows; $i++) {
			if (db_result($result,$i,'group_id') != $last_group) {
				echo '
				<TR><TD COLSPAN="2"><B>'.group_getname(db_result($result,$i,'group_id')).'</TD></TR>';
			}
			echo '
			<TR BGCOLOR="'.get_priority_color(db_result($result,$i,'priority')).'"><TD><A HREF="/bugs/?func=detailbug&group_id='.
				db_result($result,$i,'group_id').'&bug_id='.db_result($result,$i,'bug_id').
				'">'.db_result($result,$i,'bug_id').'</A></TD>'.
				'<TD>'.stripslashes(db_result($result,$i,'summary')).'</TD></TR>';

			$last_group=db_result($result,$i,'group_id');
		}
	}
	html_box1_bottom();

	/*
		Forums that are actively monitored
	*/
	$last_group=0;
	html_box1_top('Monitored Forums');
	$sql="SELECT groups.group_name,groups.group_id,forum_group_list.group_forum_id,forum_group_list.forum_name ".
		"FROM groups,forum_group_list,forum_monitored_forums ".
		"WHERE groups.group_id=forum_group_list.group_id ".
		"AND forum_group_list.group_forum_id=forum_monitored_forums.forum_id ".
		"AND forum_monitored_forums.user_id='".user_getid()."'";
	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo 'You are not monitoring any forums';
		echo db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if (db_result($result,$i,'group_id') != $last_group) {
				echo '
				<TR><TD COLSPAN="2"><B>'.db_result($result,$i,'group_name').'</TD></TR>';
			}
			if ($i % 2 == 0) {
				$row_color=' BGCOLOR="'.$GLOBALS[COLOR_LTBACK1].'"';
			} else {
				$row_color=' BGCOLOR="#FFFFFF"';
			}
			echo '
			<TR'.$row_color.'><TD ALIGN="MIDDLE"><A HREF="/forum/monitor.php?forum_id='.
				db_result($result,$i,'group_forum_id').
				'"><IMG SRC="/images/ic/trash.png HEIGHT="16" WIDTH="16" BORDER=0"></A></TD><TD WIDTH="99%"><A HREF="/forum/forum.php?forum_id='.
				db_result($result,$i,'group_forum_id').'">'.
				stripslashes(db_result($result,$i,'forum_name')).'</TD></TR>';

			$last_group=db_result($result,$i,'group_id');
		}
	}
	html_box1_bottom();

	?>
	</TD><TD VALIGN="TOP" WIDTH="50%">
	<?php
	/*
		Tasks assigned to me
	*/
	$last_group=0;
	html_box1_top('My Tasks');

	$sql="SELECT groups.group_name,project_group_list.project_name,project_group_list.group_id, ".
		"project_task.group_project_id,project_task.priority,project_task.project_task_id,project_task.summary ".
		"FROM groups,project_group_list,project_task,project_assigned_to ".
		"WHERE project_task.project_task_id=project_assigned_to.project_task_id ".
		"AND project_assigned_to.assigned_to_id='".user_getid()."' AND project_task.status_id='1'  ".
		"AND project_group_list.group_id=groups.group_id ".
		"AND project_group_list.group_project_id=project_task.group_project_id ORDER BY project_name";

	$result=db_query($sql);
	$rows=db_numrows($result);

	if ($rows > 0) {
		for ($i=0; $i < $rows; $i++) {
			if (db_result($result,$i,'group_project_id') != $last_group) {
				echo '
				<TR><TD COLSPAN="2"><B>'.db_result($result,$i,'group_name').' - '.db_result($result,$i,'project_name').'</TD></TR>';
			}
			echo '
			<TR BGCOLOR="'.get_priority_color(db_result($result,$i,'priority')).'">
				<TD><A HREF="/pm/task.php?func=detailtask&project_task_id='.
				db_result($result, $i, 'project_task_id').
				'&group_id='.db_result($result, $i, 'group_id').
				'&group_project_id='.db_result($result, $i, 'group_project_id').'">'.
				db_result($result, $i, 'project_task_id').'</TD>
				<TD>'.stripslashes(db_result($result, $i, 'summary')).'</TD></TR>';
			$last_group = db_result($result,$i,'group_project_id');
		}
	} else {
		echo '
			You have no open tasks assigned to you';
		echo db_error();
	}

	html_box1_bottom();


	/*
		DEVELOPER SURVEYS

		This needs to be updated manually to display any given survey
	*/

	$sql="SELECT * from survey_responses ".
		"WHERE survey_id='1' AND user_id='".user_getid()."' AND group_id='1'";

	$result=db_query($sql);

	if (db_numrows($result) < 1) {
		html_box1_top('Quick Survey');
		show_survey(1,1);
		html_box1_bottom();
	}


	/*
		PROJECT LIST
	*/

	html_box1_top('My Projects');
	$result = db_query("SELECT groups.group_name AS group_name,"
		. "groups.group_id AS group_id,"
		. "groups.status AS status,"
		. "user_group.admin_flags AS admin_flags "
		. "FROM groups,user_group WHERE "
		. "groups.group_id=user_group.group_id AND "
		. "user_group.user_id=" . user_getid());

	if (!$result || db_numrows($result) < 1) {
		echo "You're not in any projects";
	} else {
		while ($row_proj = db_fetch_array($result)) {
			print "<A href=\"/project/?group_id=$row_proj[group_id]\">$row_proj[group_name]</A><BR>";
		}
	}
	html_box1_bottom();

	echo '</TD></TR><TR><TD COLSPAN=2>';

	show_priority_colors_key();

	?>
	</TD>
	</TABLE>
	<?php
	site_footer(array());
	site_cleanup(array());

} else {
	exit_not_logged_in();
}

?>
