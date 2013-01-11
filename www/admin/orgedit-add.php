<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: orgedit-add.php,v 1.6 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";
session_require(array('group'=>'1','admin_flags'=>'A'));

// ########################################################

if ($GLOBALS["Submit"]) {
	if ($GLOBALS['form_orgname']) {
	db_query("INSERT INTO organization (org_name,org_type,org_url,org_descriptivetext) "
		."VALUES ('"
		.$GLOBALS['form_orgname']."','"
		.$GLOBALS['form_orgtype']."','"
		.$GLOBALS['form_orgurl']."','"
		.$GLOBALS['form_orgdesc']."')");
	} 
	session_redirect("/admin/orglist.php");
} 

site_header(array('title'=>'New Organization'));
?>

<form action="orgedit-add.php" method="post">
<p>New organization name:
<br><input type="text" name="form_orgname">
<P>Org type:
<BR><INPUT type="text" name="form_orgtype">
<P>URL:
<BR><INPUT type="text" name="form_orgurl">
<P>Descriptive Text:
<BR><TEXTAREA name="form_orgdesc" cols=60 rows=10>
</TEXTAREA>
<br><input type="submit" name="Submit" value="Submit">
</form>

<?php
site_footer(array());
site_cleanup(array());
?>
