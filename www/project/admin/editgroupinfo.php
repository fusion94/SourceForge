<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editgroupinfo.php,v 1.24 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
require "vars.php";
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

// must do this before updates to make sure group exists, and after to update info
$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) 
	exit_no_group();

$row_grp = db_fetch_array($res_grp);

// If this was a submission, make updates

if ($GLOBALS['Update']) {

	if (!db_query('UPDATE groups SET '
		."group_name='$form_group_name',"
		."homepage='$form_homepage',"
		."short_description='$form_shortdesc' WHERE "
		."group_id=$group_id")) {
		exit_error('Query Error','There was an unknown error in this query. Please email
admin@sourceforge.net with details of the problem.');
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

site_header(array(title=>"Editing Group Info",group=>$group_id));

print '<P>Editing group info for: <B>'.$row_grp[group_name].'</B>';

if ($updatesuccessful) print '<P><FONT color="#FF0000">Entries successfully updated.</FONT>';

print '<P><FORM action="editgroupinfo.php" method="post">
<INPUT type="hidden" name="group_id" value="'.$group_id.'">

<P>Descriptive Group Name:
<BR><INPUT type="text" name="form_group_name" value="'.$row_grp[group_name].'">

<P>Short Description (255 Character Max, HTML will be stripped from this description):
<BR><TEXTAREA cols=80 rows=3 wrap="virtual" name="form_shortdesc">
'.$row_grp[short_description].'</TEXTAREA>

<P>Homepage Link:
<BR>http://<INPUT type="text" name="form_homepage" value="'.$row_grp[homepage].'">

<HR>

<TABLE border="0" cellpadding="0" cellspacing="0"><TR>
<TD>
Software Environment:
<BR><I>Select all that apply.</I>
';

//environment checkboxes
$result=db_query("SELECT env_id FROM group_env WHERE group_id='$group_id'");
utils_buildcheckboxarray($SOFTENV,'form_env[]',result_column_to_array($result));


print '</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>
Languages Used:
<BR><I>Select all that apply.</I>';


//languages checkboxes
$result=db_query("SELECT language_id FROM group_language WHERE group_id='$group_id'");
utils_buildcheckboxarray($SOFTLANG,'form_lang[]',result_column_to_array($result));


print '
</TD></TR></TABLE>
<HR>
<P><INPUT type="submit" name="Update" value="Update">
</FORM>

<P><A href="/project/admin/?group_id='.$group_id.'">[Return to Project Admin]</A>
';

site_footer(array());
site_cleanup(array());
?>
