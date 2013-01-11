<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: category.php,v 1.13 2000/01/13 18:36:36 precision Exp $

require "pre.php";    // Initial db and session library, opens session
require "vars.php";
session_require(array('isloggedin'=>'1'));
require "account.php";

if ($group_id && $insert_license && $rand_hash && $form_license) {
	/*
		Hash prevents them from updating a live, existing group account
	*/
	//$form_license_other
	$sql="UPDATE groups SET license='$form_license', license_other='$form_license_other' ".
		"WHERE group_id='$group_id' AND rand_hash='__$rand_hash'";
	$result=db_query($sql);
	if (db_affected_rows($result) < 1) {
		exit_error('Error','This is an invalid state. Update query failed. <B>PLEASE</B> report to admin@sourceforge.net');
	}

} else {
	exit_error('Error','This is an invalid state. Some form variables were missing.
		If you are certain you entered everything, <B>PLEASE</B> report to admin@sourceforge.net and
		include info on your browser and platform configuration');
}

site_header(array('title'=>'Project Category'));
?>

<H2>Step 6: Category</H2>


<P><B>Project Category</B>

<P>So that visitors to the site can find your project, you should select
a category that is most appropriate to your project's purpose.
If you aren't yet familiar with the
<A href="/softwaremap/">software map</A>, you should visit it now
(right click to open it in another window). If a new category should
be created for this project, please select the category that is closest
for now, and email our staff with your new category recommendation.

<P>If you are registering for a website-only project, select "Web Site"
as your project category.

<FONT size=-1>
<FORM action="confirmation.php" method="post">
<INPUT TYPE="HIDDEN" NAME="insert_category" VALUE="y">
<INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php echo $group_id; ?>">
<INPUT TYPE="HIDDEN" NAME="rand_hash" VALUE="<?php echo $rand_hash; ?>">
Category:
<BR><?php category_popup("form_category"); ?>

<P>Your project is further categorized by the environments under
which it can be run, and the languages used.
<?php
print '<P><TABLE border="0" cellpadding="0" cellspacing="0"><TR>
<TD>
Software Environment:
<BR><I>Select all that apply.</I>
';

utils_buildcheckboxarray($SOFTENV,'form_env[]',array());

print '</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>
Languages Used:
<BR><I>Select all that apply.</I>';

utils_buildcheckboxarray($SOFTLANG,'form_lang[]',array());

print '
</TD></TR></TABLE>
';
?>
<P>
<H2><FONT COLOR="RED">Do Not Back Arrow After This Point</FONT></H2> 
<P>
<INPUT type=submit name="Submit" value="Finish Registration">
</FORM>
</FONT>

<?php
site_footer(array());
site_cleanup(array());
?>

