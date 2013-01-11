<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.64 2000/06/11 03:23:03 tperdue Exp $

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
	echo html_box1_top('My Bugs');

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
				<TR><TD COLSPAN="2"><B><A HREF="/bugs/?group_id='.
					db_result($result,$i,'group_id').'">'.
					group_getname(db_result($result,$i,'group_id')).'</A></TD></TR>';
			}
			echo '
			<TR BGCOLOR="'.get_priority_color(db_result($result,$i,'priority')).'"><TD><A HREF="/bugs/?func=detailbug&group_id='.
				db_result($result,$i,'group_id').'&bug_id='.db_result($result,$i,'bug_id').
				'">'.db_result($result,$i,'bug_id').'</A></TD>'.
				'<TD>'.stripslashes(db_result($result,$i,'summary')).'</TD></TR>';

			$last_group=db_result($result,$i,'group_id');
		}
		echo '<TR><TD COLSPAN="2">&nbsp;</TD></TR>';
	}

	/*
		Forums that are actively monitored
	*/
	$last_group=0;
	echo html_box1_middle('Monitored Forums');
	$sql="SELECT groups.group_name,groups.group_id,forum_group_list.group_forum_id,forum_group_list.forum_name ".
		"FROM groups,forum_group_list,forum_monitored_forums ".
		"WHERE groups.group_id=forum_group_list.group_id ".
		"AND forum_group_list.group_forum_id=forum_monitored_forums.forum_id ".
		"AND forum_monitored_forums.user_id='".user_getid()."' ORDER BY group_name DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '
			<H3>You are not monitoring any forums</H3>
			<P>
			If you monitor forums, you will be sent new posts in 
			the form of an email, with a link to the new message.
			<P>
			You can monitor forums by clicking &quot;Monitor Forum&quot; in 
			any given discussion forum.
			<BR>&nbsp;';
		echo db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if (db_result($result,$i,'group_id') != $last_group) {
				echo '
				<TR><TD COLSPAN="2"><B><A HREF="/forum/?group_id='.
					db_result($result,$i,'group_id').'">'.
					db_result($result,$i,'group_name').'</A></TD></TR>';
			}
			echo '
			<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD ALIGN="MIDDLE"><A HREF="/forum/monitor.php?forum_id='.
				db_result($result,$i,'group_forum_id').
				'"><IMG SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" '.
				'BORDER=0"></A></TD><TD WIDTH="99%"><A HREF="/forum/forum.php?forum_id='.
				db_result($result,$i,'group_forum_id').'">'.
				stripslashes(db_result($result,$i,'forum_name')).'</A></TD></TR>';

			$last_group=db_result($result,$i,'group_id');
		}
		echo '<TR><TD COLSPAN="2">&nbsp;</TD></TR>';
	}

	/*
		Filemodules that are actively monitored
	*/
	$last_group=0;
	echo html_box1_middle('Monitored FileModules');
	$sql="SELECT groups.group_name,groups.group_id,filemodule.module_name,filemodule_monitor.filemodule_id ".
		"FROM groups,filemodule_monitor,filemodule ".
		"WHERE groups.group_id=filemodule.group_id ".
		"AND filemodule.filemodule_id=filemodule_monitor.filemodule_id ".
		"AND filemodule_monitor.user_id='".user_getid()."' ORDER BY group_name DESC";
	$result=db_query($sql);
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '
			<H3>You are not monitoring any files</H3>
			<P>
			If you monitor files, you will be sent new release notices via
			email, with a link to the new file on our download server.
			<P>
			You can monitor files by visiting a project\'s &quot;Summary Page&quot; 
			and clicking on the check box in the files section.
			<BR>&nbsp;';
		echo db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			if (db_result($result,$i,'group_id') != $last_group) {
				echo '
				<TR><TD COLSPAN="2"><B><A HREF="/project/?group_id='.
					db_result($result,$i,'group_id').'">'.
					db_result($result,$i,'group_name').'</A></TD></TR>';
			}
			echo '
			<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD ALIGN="MIDDLE"><A HREF="/project/filemodule_monitor.php?filemodule_id='.
				db_result($result,$i,'filemodule_id').
				'"><IMG SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" '.
				'BORDER=0"></A></TD><TD WIDTH="99%"><A HREF="/project/filelist.php?group_id='.
				db_result($result,$i,'group_id').'">'.
				stripslashes(db_result($result,$i,'module_name')).'</A></TD></TR>';

			$last_group=db_result($result,$i,'group_id');
		}
		echo '<TR><TD COLSPAN="2">&nbsp;</TD></TR>';
	}

	echo html_box1_bottom();

	?>
	</TD><TD VALIGN="TOP" WIDTH="50%">
	<?php
	/*
		Tasks assigned to me
	*/
	$last_group=0;
	echo html_box1_top('My Tasks');

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
				<TR><TD COLSPAN="2"><B><A HREF="/pm/task.php?group_id='.
					db_result($result,$i,'group_id').'&group_project_id='.
					db_result($result,$i,'group_project_id').'">'.
					db_result($result,$i,'group_name').' - '.
					db_result($result,$i,'project_name').'</A></TD></TR>';
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
		echo '<TR><TD COLSPAN="2">&nbsp;</TD></TR>';
	} else {
		echo '
			You have no open tasks assigned to you';
		echo db_error();
	}

	/*
		DEVELOPER SURVEYS

		This needs to be updated manually to display any given survey
	*/

	$sql="SELECT * from survey_responses ".
		"WHERE survey_id='1' AND user_id='".user_getid()."' AND group_id='1'";

	$result=db_query($sql);

	echo html_box1_middle('Quick Survey');

	if (db_numrows($result) < 1) {
		show_survey(1,1);
		echo '<TR><TD COLSPAN="2">&nbsp;</TD></TR>';
	} else {
		echo 'You have taken your developer survey';
	}

	/*
	       Personal bookmarks
	*/
	echo html_box1_middle('My Bookmarks');

	$result = db_query("SELECT bookmark_url, bookmark_title, bookmark_id from user_bookmarks where ".
		"user_id='". user_getid() ."' ORDER BY bookmark_title");
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo '
			<H3>You currently do not have any bookmarks saved</H3>';
		echo db_error();
	} else {
		for ($i=0; $i<$rows; $i++) {
			echo '
				<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD ALIGN="MIDDLE">
				<A HREF="/my/bookmark_delete.php?bookmark_id='. db_result($result,$i,'bookmark_id') .'">
				<IMG SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" BORDER="0"></A></TD>
				<TD><B><A HREF="'. db_result($result,$i,'bookmark_url') .'">'.
				db_result($result,$i,'bookmark_title') .'</A></B> &nbsp;
				<SMALL><A HREF="/my/bookmark_edit.php?bookmark_id='. db_result($result,$i,'bookmark_id') .'">[Edit]</A></SMALL></TD</TR>';
		}
	}


	/*
		PROJECT LIST
	*/

	echo html_box1_middle('My Projects');
	$result = db_query("SELECT groups.group_name,"
		. "groups.group_id,"
		. "groups.unix_group_name,"
		. "groups.status,"
		. "user_group.admin_flags "
		. "FROM groups,user_group WHERE "
		. "groups.group_id=user_group.group_id AND "
		. "user_group.user_id=" . user_getid());
	$rows=db_numrows($result);
	if (!$result || $rows < 1) {
		echo "You're not a member of any projects";
	} else {
		for ($i=0; $i<$rows; $i++) {
			echo '
				<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD ALIGN="MIDDLE">
				<A href="rmproject.php?group_id='. db_result($result,$i,'group_id') .'"><IMG SRC="/images/ic/trash.png" HEIGHT="16" WIDTH="16" BORDER="0"></A></TD>
				<TD><A href="/projects/'. db_result($result,$i,'unix_group_name') .'/">'. db_result($result,$i,'group_name') .'</A></TD></TR>';
		}
	}
	echo html_box1_bottom();

	echo '</TD></TR><TR><TD COLSPAN=2>';

	echo show_priority_colors_key();

	?>
	</TD>
	</TABLE>
	<?php
	site_footer(array());

} else {

	exit_not_logged_in();

}

?>
