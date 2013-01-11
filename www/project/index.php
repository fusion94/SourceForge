<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.194 2000/01/13 18:36:36 precision Exp $

require ('pre.php');    
require ('vote_function.php');
require ('vars.php');
require ('../news/news_utils.php');

/*
	Project Summary Page
	Written by dtype Oct. 1999
*/

if ((!$group_id) && $form_grp) {
	$group_id=$form_grp;
}

if (!$group_id) {
	exit_error("Missing Group Argument","A group must be specified for this page.");
}

// get info for project
$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");

if (db_numrows($res_grp) < 1) {
	exit_error("Invalid Group","That group does not exist.");
} else {
	$row_grp = db_fetch_array($res_grp);
}

if ($row_grp[public]==0) {
	//if its a private group, you must be a member of that group
	session_require(array('group'=>$group_id));
}

if (!(($row_grp[status] == 'A') || ($row_grp[status] == 'H'))) {
	//only SF group can view non-active, non-holding groups
	session_require(array('group'=>1));
}

site_header(array(title=>"Project Info",group=>$group_id));

// ######################### TABS
html_tabs("home",$group_id);

// ########################################## top area, not in box 
print '<P>'.group_fullname($group_id);
// admin info
$res_admin = db_query("SELECT user.user_id AS user_id,user.user_name AS user_name FROM user,user_group "
	. "WHERE user_group.user_id=user.user_id AND user_group.group_id=$group_id AND "
	. "user_group.admin_flags = 'A'");
if ($row_grp[status] == 'H') {
	print "<P>NOTE: This project entry is maintained by the SourceForge staff. We are not "
		. "the official site "
		. "for this product. Additional copyright information may be found on this project's homepage.\n";
}

if ($row_grp[short_description]) {
	print "<P>" . stripslashes($row_grp[short_description]);
} else {
	print "<P>This project has not yet submitted a description.";
}

print '<TABLE width="100%" cellpadding="0" cellspacing="0" border="0"><TR><TD width="99%">';
print "<BR>License: " . $LICENSE["$row_grp[license]"];

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
html_box1_top("Public Areas"); ?>

&nbsp;<BR>This project has many places for you
to explore and participate. The icons displayed below are also
available at the top of the page for easy navigation.
<?php

// ################# Homepage Link

print "<HR><A href=\"http://" . $row_grp[homepage] . "\">";
html_image("ic/home.png",array());
print '&nbsp;Project Homepage</A>';
print '<BR><I>This home page points to the official page for this project,
which may or may not be hosted at SourceForge.</I>';

// ################## forums

print '<HR><A href="/forum/?group_id='.$group_id.'">';
html_image("ic/notes.png",array()); 
print '&nbsp;Public Forums</A>';
$res_count = db_query("SELECT count(forum.msg_id) AS count FROM forum,forum_group_list WHERE "
	. "forum_group_list.group_id=$group_id AND forum.group_forum_id=forum_group_list.group_forum_id "
	. "AND forum_group_list.is_public=1");
$row_count = db_fetch_array($res_count);
print "<BR><I>There are now <B>$row_count[count]</B> messages in ";

$res_count = db_query("SELECT count(*) AS count FROM forum_group_list WHERE group_id=$group_id "
	. "AND is_public=1");
$row_count = db_fetch_array($res_count);
print "<B>$row_count[count]</B> forums</I>\n";

// ##################### Bug tracking (only for Active)

if (($row_grp[status] == 'A') && ($row_grp[option_bugs])) {
	print '<HR><A href="/bugs/?group_id='.$group_id.'">';
	html_image("ic/bug.png",array()); 
	print '&nbsp;Bug Tracking</A>';
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id AND status_id != 3");
	$row_count = db_fetch_array($res_count);
	print "<BR><I>There are <B>$row_count[count]</B>";
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	print " open bugs, <B>$row_count[count]</B> total.";
}

// ##################### Mailing lists (only for Active)

if ($row_grp[status] == 'A') {
	print '<HR><A href="/mail/?group_id='.$group_id.'">';
	html_image("ic/mail.png",array()); 
	print '&nbsp;Mailing Lists</A>';
	$res_count = db_query("SELECT count(*) AS count FROM mail_group_list WHERE group_id=$group_id AND is_public=1");
	$row_count = db_fetch_array($res_count);
	print "<BR><I>There are <B>$row_count[count]</B> public mailing lists.";
}

// ######################### Surveys (only for Active)

if ($row_grp[status] == 'A') {
	print "<HR><A href=\"/survey/?group_id=$group_id\">";
	html_image("ic/survey.png",array());
	print " Public Surveys</A>";
	print '<BR><I>Surveys may be defined by the project administrator
	to gather user input about this project.</I>';
}

// ######################### CVS (only for Active)

if ($row_grp[status] == 'A') {
	print "<HR><A href=\"/cvs/?group_id=$group_id\">";
	html_image("ic/convert.png",array());
	print " CVS Repository</A>";
	print '<BR><I>The CVS repository is a place for this project to store
	its source code. Developers have access to change this master repository,
	while anonymous users may browse the most recent development version
	of this project.</I>';
}

// ######################## AnonFTP (only for Active

if ($row_grp[status] == 'A') {
	print "<HR>";
	print "<A href=\"ftp://" . $row_grp[http_domain] . "/pub/$row_grp[unix_group_name]/ " . "\">";
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

html_box1_top("Developer Info");

echo '&nbsp;<BR></TD></TR>';

if (db_numrows($res_admin)) {

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
<A HREF="memberlist.php?group_id=<?php print $group_id; ?>">[View Members]</A>
<?php 

html_box1_bottom();

// ############################# File Releases

html_box1_top("File Releases"); 
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
				. "AND release_version='$row_modules[recent_filerelease]'");
			if (db_numrows($res_files) < 1) {
				print "<BR>No releases.";
			} else {
				print "<BR>Latest release is $row_modules[recent_filerelease].";
				print "<BR>View: ";
				print "<A href=\"filenotes.php?group_id=$group_id&form_filemodule_id="
					. "$row_modules[filemodule_id]&form_release_version="
					. urlencode($row_modules[recent_filerelease])
					. "\">[Release Notes & Changelog]</A>";
				print "<BR>Download: ";
				// print all file types
				while ($row_files = db_fetch_array($res_files)) {
					print "<A href=\"/download.php?fileid=$row_files[filerelease_id]\">"
						. "[$row_files[file_type]]</A>";
				}
			}
			print "<P>";
		}
?><P align=center><A href="filelist.php?group_id=<?php print $group_id; ?>">[View ALL Project Files]</A><?php
	} // else modules exist

html_box1_bottom(); 

echo '<P>';

//latest news items for this project

echo news_show_latest($group_id);

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

?>
</TD>

</TR></TABLE>

<?php

site_footer(array());
site_cleanup(array());
?>
