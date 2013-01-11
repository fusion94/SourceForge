<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editgroupinfo.php,v 1.31 2000/04/24 13:28:44 dtype Exp $

require ('pre.php');
require ('vars.php');
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

// must do this before updates to make sure group exists, and after to update info
$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) 
	exit_no_group();

$row_grp = db_fetch_array($res_grp);

// If this was a submission, make updates

if ($Update) {

	if (!$use_bugs) {
		$use_bugs=0;
	}
	if (!$use_mail) {
		$use_mail=0;
	}
	if (!$use_survey) {
		$use_survey=0;
	}
	if (!$use_patch) {
		$use_patch=0;
	}
	if (!$use_forum) {
		$use_forum=0;
	}
	if (!$use_pm) {
		$use_pm=0;
	}
	if (!$use_cvs) {
		$use_cvs=0;
	}
	if (!$use_news) {
		$use_news=0;
	}
	if (!$use_support) {
		$use_support=0;
	}

	$result=db_query('UPDATE groups SET '
		."group_name='$form_group_name',"
		."homepage='$form_homepage',"
		."short_description='$form_shortdesc',"
		."use_bugs='$use_bugs',"
		."use_mail='$use_mail',"
		."use_survey='$use_survey',"
		."use_patch='$use_patch',"
		."use_forum='$use_forum',"
		."use_pm='$use_pm',"
		."use_cvs='$use_cvs',"
		."use_news='$use_news',"
		."use_support='$use_support'"
		." WHERE group_id=$group_id");

	if (!$result || db_affected_rows($result) < 1) {
		$feedback .= ' UPDATE FAILED OR NO DATA CHANGED! ';
	} else {
		$feedback .= ' UPDATE SUCCESSFUL ';
	}

	// update info for page
	$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
	if (db_numrows($res_grp) < 1) exit_no_group();
	$row_grp = db_fetch_array($res_grp);
}

project_admin_header(array('title'=>'Editing Group Info','group'=>$group_id));

print '<P>Editing group info for: <B>'.$row_grp[group_name].'</B>';

if ($updatesuccessful) print '<P><FONT color="#FF0000">Entries successfully updated.</FONT>';

print '
<P>
<FORM action="'.$PHP_SELF.'" method="post">
<INPUT type="hidden" name="group_id" value="'.$group_id.'">

<P>Descriptive Group Name:
<BR><INPUT type="text" name="form_group_name" value="'.$row_grp[group_name].'">

<P>Short Description (255 Character Max, HTML will be stripped from this description):
<BR><TEXTAREA cols=80 rows=3 wrap="virtual" name="form_shortdesc">
'.$row_grp[short_description].'</TEXTAREA>

<P>Homepage Link:
<BR>http://<INPUT type="text" name="form_homepage" value="'.$row_grp[homepage].'">

<HR>

<H3>Active Features:</H3>
<P>
';
/*
	Show the options that this project is using
*/

echo '
	<B>Use Bug Tracker:</B> <INPUT TYPE="CHECKBOX" NAME="use_bugs" VALUE="1"'.( ($row_grp['use_bugs']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use Mailing Lists:</B> <INPUT TYPE="CHECKBOX" NAME="use_mail" VALUE="1"'.( ($row_grp['use_mail']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use Surveys:</B> <INPUT TYPE="CHECKBOX" NAME="use_survey" VALUE="1"'.( ($row_grp['use_survey']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use Patch Manager:</B> <INPUT TYPE="CHECKBOX" NAME="use_patch" VALUE="1"'.( ($row_grp['use_patch']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use Forums:</B> <INPUT TYPE="CHECKBOX" NAME="use_forum" VALUE="1"'.( ($row_grp['use_forum']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use Project/Task Manager:</B> <INPUT TYPE="CHECKBOX" NAME="use_pm" VALUE="1"'.( ($row_grp['use_pm']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use CVS:</B> <INPUT TYPE="CHECKBOX" NAME="use_cvs" VALUE="1"'.( ($row_grp['use_cvs']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
	<B>Use News:</B> <INPUT TYPE="CHECKBOX" NAME="use_news" VALUE="1"'.( ($row_grp['use_news']==1) ? ' CHECKED' : '' ).'><BR>';
echo '
	<B>Use Support:</B> <INPUT TYPE="CHECKBOX" NAME="use_support" VALUE="1"'.( ($row_grp['use_support']==1) ? ' CHECKED' : '' ).'><BR>';

echo '
<HR>
<P><INPUT type="submit" name="Update" value="Update">
</FORM>
';

project_admin_footer(array());
?>
