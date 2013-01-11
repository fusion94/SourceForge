<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: project_home.php,v 1.24 2000/07/12 21:01:40 tperdue Exp $

require ('vote_function.php');
require ('vars.php');
require ($DOCUMENT_ROOT.'/news/news_utils.php');
require ('trove.php');

//get the group result set
$res_grp=group_get_result($group_id);

if (db_numrows($res_grp) < 1) {
	exit_error("Invalid Group","That group does not exist.");
}

if (db_result($res_grp,0,'is_public')==0) {
	//if its a private group, you must be a member of that group
	session_require(array('group'=>$group_id));
}

if (!((db_result($res_grp,0,'status') == 'A') || (db_result($res_grp,0,'status') == 'H'))) {
	//only SF group can view non-active, non-holding groups
	session_require(array('group'=>'1'));
}

$title = 'Project Info - '.group_getname($group_id);

site_header(array('title'=>$title,'group'=>$group_id));

// ######################### TABS
html_tabs("home",$group_id);

// ########################################## top area, not in box 
$res_admin = db_query("SELECT user.user_id AS user_id,user.user_name AS user_name "
	. "FROM user,user_group "
	. "WHERE user_group.user_id=user.user_id AND user_group.group_id=$group_id AND "
	. "user_group.admin_flags = 'A'");

if (db_result($res_grp,0,'status') == 'H') {
	print "<P>NOTE: This project entry is maintained by the SourceForge staff. We are not "
		. "the official site "
		. "for this product. Additional copyright information may be found on this project's homepage.\n";
}

if (db_result($res_grp,0,'short_description')) {
	print "<P>" . db_result($res_grp,0,'short_description');
} else {
	print "<P>This project has not yet submitted a description.";
}

// table: left side trove info, right side voting

print '<TABLE width="100%" cellpadding="0" cellspacing="0" border="0"><TR><TD width="99%">';
// trove info
print '&nbsp;<BR>';
trove_getcatlisting($group_id,0,1);
print '<BR>&nbsp;';
print '</TD><TD align="LEFT" nowrap>';
print vote_show_release_radios($group_id,1);
print '</TD></TR></TABLE>
	';

// ########################################### end top area

// two column deal
?>
<TABLE width=100% cellspacing=0 cellpadding=0 border=0><TR valign=top>

<TD width=50%>

<?php 
// ############################## PUBLIC AREAS
html_box1_top("Public Areas"); 

// ################# Homepage Link

print "<A href=\"http://" . db_result($res_grp,0,'homepage') . "\">";
html_image("ic/home.png",array());
print '&nbsp;Project Homepage</A>';
print '<BR><I>This home page points to the official page for this project,
which may or may not be hosted at SourceForge.</I>';

// ################## forums

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_forum'))) {
	print '<HR><A href="/forum/?group_id='.$group_id.'">';
	html_image("ic/notes.png",array()); 
	print '&nbsp;Public Forums</A>';
	$res_count = db_query("SELECT count(forum.msg_id) AS count FROM forum,forum_group_list WHERE "
		. "forum_group_list.group_id=$group_id AND forum.group_forum_id=forum_group_list.group_forum_id "
		. "AND forum_group_list.is_public=1");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B> messages in ";

	$res_count = db_query("SELECT count(*) AS count FROM forum_group_list WHERE group_id=$group_id "
		. "AND is_public=1");
	$row_count = db_fetch_array($res_count);
	print "<B>$row_count[count]</B> forums )\n";
/*
	$sql="SELECT * FROM forum_group_list WHERE group_id='$group_id' AND is_public=1";
	$res2 = db_query ($sql);
	$rows = db_numrows($res2);
	for ($j = 0; $j < $rows; $j++) {
		echo '<BR> &nbsp; - <A HREF="forum.php?forum_id='.db_result($res2, $j, 'group_forum_id').'&et=0">'.
			db_result($res2, $j, 'forum_name').'</A> ';
		//message count
		echo '('.db_result(db_query("SELECT count(*) FROM forum WHERE group_forum_id='".db_result($res2, $j, 'group_forum_id')."'"),0,0).' msgs)';
	}
*/
}

// ##################### Bug tracking (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_bugs'))) {
	print '<HR><A href="/bugs/?group_id='.$group_id.'">';
	html_image("ic/bug.png",array()); 
	print '&nbsp;Bug Tracking</A>';
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id AND status_id != 3");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B>";
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	print " open bugs, <B>$row_count[count]</B> total )";
}

// ##################### Support Manager (only for Active)
 
if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_support'))) {
	print '
	<HR>
	<A href="/support/?group_id='.$group_id.'">';
	html_image("ic/support.png",array());
	print '&nbsp;Tech Support Manager</A>';
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id AND support_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open requests, <B>$row_count[count]</B> total )";
}

// ##################### Doc Manager (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_docman'))) {
	print '
	<HR>
	<A href="/docman/?group_id='.$group_id.'">';
	html_image("ic/docman.png",array());
	print '&nbsp;DocManager: Project Documentation</A>';
/*
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id AND support_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open requests, <B>$row_count[count]</B> total )";
*/
}

// ##################### Patch Manager (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_patch'))) {
	print '
		<HR>
		<A href="/patch/?group_id='.$group_id.'">';
	html_image("ic/patch.png",array());
	print '&nbsp;Patch Manager</A>';
	$res_count = db_query("SELECT count(*) AS count FROM patch WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM patch WHERE group_id=$group_id AND patch_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open patches, <B>$row_count[count]</B> total )";
}

// ##################### Mailing lists (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_mail'))) {
	print '<HR><A href="/mail/?group_id='.$group_id.'">';
	html_image("ic/mail.png",array()); 
	print '&nbsp;Mailing Lists</A>';
	$res_count = db_query("SELECT count(*) AS count FROM mail_group_list WHERE group_id=$group_id AND is_public=1");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B> public mailing lists )";
}

// ##################### Task Manager (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_pm'))) {
	print '<HR><A href="/pm/?group_id='.$group_id.'">';
	html_image("ic/index.png",array());
	print '&nbsp;Project/Task Manager</A>';
	$sql="SELECT * FROM project_group_list WHERE group_id='$group_id' AND is_public=1";
	$result = db_query ($sql);
	$rows = db_numrows($result);
	if (!$result || $rows < 1) {
		echo '<BR><I>There are no public projects available</I>';
	} else {
		for ($j = 0; $j < $rows; $j++) {
			echo '
			<BR> &nbsp; - <A HREF="/pm/task.php?group_project_id='.db_result($result, $j, 'group_project_id').
			'&group_id='.$group_id.'&func=browse">'.db_result($result, $j, 'project_name').'</A>';
		}

	}
}

// ######################### Surveys (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_survey'))) {
	print "<HR><A href=\"/survey/?group_id=$group_id\">";
	html_image("ic/survey.png",array());
	print " Surveys</A>";
	$sql="SELECT count(*) from surveys where group_id='$group_id' AND is_active='1'";
	$result=db_query($sql);
	echo ' ( <B>'.db_result($result,0,0).'</B> surveys )';
}

// ######################### CVS (only for Active)

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_cvs'))) {
	print "<HR><A href=\"/cvs/?group_id=$group_id\">";
	html_image("ic/convert.png",array());
	print " CVS Repository</A>";
	print '<BR><I>The CVS repository is a place for this project to store
	its source code. Developers have access to change this master repository,
	while anonymous users may browse the most recent development version
	of this project.</I>';
}

// ######################## AnonFTP (only for Active)

if (db_result($res_grp,0,'status') == 'A') {
	print "<HR>";
	print "<A href=\"ftp://" . db_result($res_grp,0,'http_domain') . "/pub/".db_result($res_grp,0,'unix_group_name')."/\">";
	print "Anonymous FTP Space</A>";
	print '<BR><I>Projects may choose to have files other than their main
	releases available via anonymous FTP.</I>';
}

html_box1_bottom();

// COLUMN BREAK

?>

</TD>
<TD>&nbsp;</TD>
<TD width=50%>

<?php



// ########################### Developers on this project

echo html_box1_top("Developer Info",0,$GLOBALS['COLOR_LTBACK2']);

echo '&nbsp;<BR></TD></TR>';

if (db_numrows($res_admin) > 0) {

	?>
	<TR valign=top>
	<TD>Project Admins:</TD>
	<TD>
	<?php
		while ($row_admin = db_fetch_array($res_admin)) {
			print "<A href=\"/developer/?form_dev=$row_admin[user_id]\">$row_admin[user_name]</A><BR>";
		}
	?>
	</TD>
	</TR>
	<?php

}

?>
<TR valign=top>
<TD>Developers:</TD>
<TD><?php
//count of developers on this project
$res_count = db_query("SELECT user_id FROM user_group WHERE group_id=$group_id");
print db_numrows($res_count);

?>
<A HREF="/project/memberlist.php?group_id=<?php print $group_id; ?>">[View Members]</A>
<?php 

// ############################# File Releases

echo html_box1_middle('File Releases',$GLOBALS['COLOR_LTBACK2']); 
	print "&nbsp;<BR>";
	$res_modules = db_query("SELECT filemodule_id,module_name,recent_filerelease "
		. "FROM filemodule WHERE group_id=$group_id");
	$unix_group_name = group_getunixname($group_id);

	if (db_numrows($res_modules) < 1) {
		print "This project has not defined any file release modules.";
	} else { // modules exist 
		while ($row_modules = db_fetch_array($res_modules)) {
			html_image("ic/save16.png",array());
			print " Module Name: <B>$row_modules[module_name]</B>";
			$res_files = db_query("SELECT filerelease_id,filename,file_type,downloads "
				. "FROM filerelease WHERE filemodule_id=$row_modules[filemodule_id] "
				. "AND release_version='$row_modules[recent_filerelease]' "
				. "AND status='A'");
			if (db_numrows($res_files) < 1) {
				print "<BR>No releases.";
			} else {
				print "<BR>Latest release is $row_modules[recent_filerelease].";
				print "<BR>View: ";
				print "<A href=\"/project/filenotes.php?group_id=$group_id&form_filemodule_id="
					. "$row_modules[filemodule_id]&form_release_version="
					. urlencode($row_modules['recent_filerelease'])
					. "\">[Release Notes & Changelog]</A>";
				print "<BR>Download: ";
				// print all file types
				while ($row_files = db_fetch_array($res_files)) {
					print "<A href=\"http://$GLOBALS[sys_download_host]/$unix_group_name/$row_files[filename]\">"
						. "[$row_files[file_type]]</A>";
				}
			}
			echo '<P>
				<A HREF="/project/filemodule_monitor.php?filemodule_id='.
				$row_modules['filemodule_id'].'">'; 
			echo html_image("ic/check.png",array()).' Monitor This Module</A>'.
				' Receive an email update when a new file is released in this module.';
			print '<P>';
		}
		?><P align=center><A href="/project/filelist.php?group_id=<?php print $group_id; ?>">[View ALL Project Files]</A><?php
	}


//latest news items for this project

if ((db_result($res_grp,0,'status') == 'A') && (db_result($res_grp,0,'use_news'))) {
	echo html_box1_middle('Latest News',$GLOBALS['COLOR_LTBACK2']);

	echo news_show_latest($group_id,10,false);
}
/*
	Show a Survey
	This needs to be updated manually to display any given survey
* /

$sql="SELECT * from survey_responses WHERE survey_id=3 AND user_id='".user_getid()."' AND group_id='$group_id'";
$result=db_query($sql);

if (db_numrows($result) < 1) {
	html_box1_top('Quick Survey');
	show_survey($group_id,3);
	html_box1_bottom();
}
*/

echo html_box1_bottom();

?>
</TD>

</TR></TABLE>

<?php

site_footer(array());

?>
