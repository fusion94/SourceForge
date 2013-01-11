<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: addfile.php,v 1.24 2000/07/12 21:01:41 tperdue Exp $

require ('pre.php');    
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));
$menuarray = array();

project_admin_header(array('title'=>'Release New File Version','group'=>$group_id));

html_box1_top("Releasing a New File Version: " . group_getname($group_id) ); ?>
&nbsp;<BR>
Before using this form, you must first send us the file to be released.
Send the file via anonymous ftp to <B><?php echo $GLOBALS['sys_download_host']; ?></B>. Upload the file
to the <B>/incoming</B> directory. There are NO downloads from this ftp server.

<P>Once you have uploaded the file, enter the information below and submit.

<P><B>NOTE:</B> You may see files here from other projects. Posting them to your
own project will only be temporary and will result in the loss of all
site privileges.
<?php html_box1_bottom(); ?>

<FORM action="addfile_basicinfo.php" method="post">
<P>Filename:
<BR><SELECT name="form_filename"> 
<?php
$dirhandle = opendir($FTPINCOMING_DIR);
while ($file = readdir($dirhandle)) {
	if ($file[0] != ".") {
		$atleastone = 1;
		print "<OPTION value=\"$file\">$file\n";
	} 
}
if (!$atleastone) {
	print "<OPTION>No available files\n";
}
?>
</SELECT>
<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<BR><INPUT type="submit" name="Submit" value="Submit">
</FORM>

<?php
project_admin_footer(array());
?>
