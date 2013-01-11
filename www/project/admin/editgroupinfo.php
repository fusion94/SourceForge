<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editgroupinfo.php,v 1.28 2000/01/26 13:54:40 tperdue Exp $

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
		."use_news='$use_news'"
		." WHERE group_id=$group_id");

	if (!$result || db_affected_rows($result) < 1) {
		$feedback .= ' UPDATE FAILED OR NO DATA CHANGED! ';
	} else {
		$feedback .= ' UPDATE SUCCESSFUL ';
	}
/*
	TIMS CHANGES
*/
	db_query("DELETE FROM group_env WHERE group_id='$group_id'");
	db_query("DELETE FROM group_language WHERE group_id='$group_id'");

	// make updates for software environment and languages
	if (count($form_env) < 1) {
		$form_env[]=1;
	}
	for ($i=0; $i<count($form_env); $i++) {
		db_query("INSERT INTO group_env (group_id,env_id) VALUES ('$group_id','$form_env[$i]')");
	}

	if (count($form_lang) < 1) {
		$form_lang[]=1;
	}
	for ($i=0; $i<count($form_lang); $i++) {
		db_query("INSERT INTO group_language (group_id,language_id) VALUES ('$group_id','$form_lang[$i]')");
	}
/*
	END TIMS CHANGES
*/

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

<TABLE border="0" cellpadding="0" cellspacing="0">
<TR>
<TD VALIGN="TOP">
	<H3>Software Environment:</H3>
	<BR>
	<I>Select all that apply.</I>
';

//environment checkboxes
$result=db_query("SELECT env_id FROM group_env WHERE group_id='$group_id'");
utils_buildcheckboxarray($SOFTENV,'form_env[]',result_column_to_array($result));


print '
</TD>
<TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
<TD VALIGN="TOP">
	<H3>Languages Used:</H3>
	<BR>
	<I>Select all that apply.</I>';


//languages checkboxes
$result=db_query("SELECT language_id FROM group_language WHERE group_id='$group_id'");
utils_buildcheckboxarray($SOFTLANG,'form_lang[]',result_column_to_array($result));


print '
</TD>
<TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
<TD>
<H3>Active Features:</H3>
<P>
';
/*
	Show the options that this project is using
*/

echo '
	<B>Bug Tracker:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_bugs" VALUE="1"'.( ($row_grp['use_bugs']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_bugs" VALUE="0"'.( ($row_grp['use_bugs']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>Mailing Lists:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_mail" VALUE="1"'.( ($row_grp['use_mail']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_mail" VALUE="0"'.( ($row_grp['use_mail']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>Surveys:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_survey" VALUE="1"'.( ($row_grp['use_survey']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_survey" VALUE="0"'.( ($row_grp['use_survey']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>Patch Manager:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_patch" VALUE="1"'.( ($row_grp['use_patch']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_patch" VALUE="0"'.( ($row_grp['use_patch']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>Forums:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_forum" VALUE="1"'.( ($row_grp['use_forum']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_forum" VALUE="0"'.( ($row_grp['use_forum']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>Project/Task Manager:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_pm" VALUE="1"'.( ($row_grp['use_pm']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_pm" VALUE="0"'.( ($row_grp['use_pm']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>CVS:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_cvs" VALUE="1"'.( ($row_grp['use_cvs']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_cvs" VALUE="0"'.( ($row_grp['use_cvs']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
	<B>News:</B><BR>
	<INPUT TYPE="RADIO" NAME="use_news" VALUE="1"'.( ($row_grp['use_news']==1) ? ' CHECKED' : '' ).'> Use<BR>
	<INPUT TYPE="RADIO" NAME="use_news" VALUE="0"'.( ($row_grp['use_news']==0) ? ' CHECKED' : '' ).'> Don\'t Use<P>';

echo '
</TD>
</TR></TABLE>
<HR>
<P><INPUT type="submit" name="Update" value="Update">
</FORM>
';

project_admin_footer(array());
?>
