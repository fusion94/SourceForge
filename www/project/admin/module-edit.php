<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: module-edit.php,v 1.10 2000/01/26 10:44:32 tperdue Exp $

require "pre.php";    
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

$res_module = db_query("SELECT * FROM filemodule WHERE filemodule_id=$form_filemodule_id");
$row_module = db_fetch_array($res_module);

if ($row_module[group_id] != $group_id) {
	exit_error("Hack attempt","You are trying to edit a file module which is not yours.");
}

if ($GLOBALS[Submit] && $form_modulename) {
	// unix account if not one
	db_query("UPDATE filemodule SET module_name='$form_modulename',"
		. "recent_filerelease='$form_recent_filerelease' WHERE filemodule_id=$form_filemodule_id");
	session_redirect ("/project/admin/?group_id=$group_id");
}

project_admin_header(array('title'=>'Edit File Module','group'=>$group_id));
?>
<P>Editing file module: <B><?php print $row_module[module_name]; ?></B>

<P><FORM action="module-edit.php" method="post">
New Module Name:
<BR><INPUT type="text" name="form_modulename" value="<?php print $row_module[module_name]; ?>">
<P>Most Recent Release (displayed on main project page):
<BR><SELECT name="form_recent_filerelease">
<?php
	$res_release = db_query("SELECT release_version FROM filerelease WHERE group_id=$group_id "
		. "AND filemodule_id=$form_filemodule_id");
	// initialize this array
	$array_release = array();
	while ($row_release = db_fetch_array($res_release)) {
		$releasever = $row_release[release_version];
		$array_release[$releasever] = 1;
	}
	while (list ($k,$v) = each ($array_release)) {
		print "<OPTION value=\"$k\"";
		if ($row_module[recent_filerelease] == $k) print " selected";
		print ">$k\n";
	}
?>
</SELECT>
<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<INPUT type="hidden" name="form_filemodule_id" value="<?php print $form_filemodule_id; ?>">
<BR><INPUT type="submit" name="Submit" value="Submit">
</FORM>

<?php
project_admin_footer(array());
?>
