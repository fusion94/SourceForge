<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: addfile_basicinfo.php,v 1.33 2000/01/26 10:44:32 tperdue Exp $

require "pre.php";    
require "paths.php";
require "filechecks.php";
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

// determine file type and test
$form_filetype = filechecks_getfiletype($form_filename);

$form_filename = chop($form_filename);

// check for duplicate filename
$res_samename = db_query("SELECT filerelease_id FROM filerelease "
	. "WHERE filename='$form_filename' AND group_id=$group_id");
if (db_numrows($res_samename) > 0) {
	exit_error("Duplicate Filename",'A file with that name already exists for this group.');
}

project_admin_header(array('title'=>"File Release - Basic Information",'group'=>$group_id));
?>

<?php html_box1_top("New File Release for " . group_getname($group_id) . ": " . $form_filename); ?>
&nbsp;<BR>
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR valign=top>
<TD>Filename:</TD>
<TD><B><?php print $form_filename; ?></B></TD>
</TR>
<TR valign=top>
<TD>File Size:</TD>
<TD><B><?php print filesize($FTPINCOMING_DIR . "/" . $form_filename) . " bytes"; ?></B></TD>
</TR>
<TR valign=top>
<TD>File Upload Time:</TD>
<TD><B><?php print date("h:i A - F d, Y",filemtime($FTPINCOMING_DIR . "/" . $form_filename)); ?></B></TD>
</TR>
<TR valign=top>
<TD>File Type:</TD>
<TD><B><?php print $form_filetype; ?></B></TD>
</TR>
</TABLE>
<?php html_box1_bottom(); ?>

<FORM action="addfile_done.php" method=post>
<P>Release Version Number
<BR><I>examples: 0.9.7, 1.0b4, 3.02, 1.0 pre4</I>
<BR><INPUT type=text name="form_release_version">

<P>Release Date
<BR><I>(YYYY-MM-DD) example: 1999-10-25. 
<BR><B><FONT color="red">Leave blank for TODAY's date/time.</FONT></B></I>
<BR><INPUT type=text name="form_release_time">

<P>Module Name
<BR><I>Use the same module name for subsequent releases of the same product.
If no modules are defined, or you need to define a new one, visit 
<A href="/project/admin/?group_id=<?php print $group_id; ?>">project administration</A>
to define a module.</I>
<BR><SELECT name=form_filemodule>
<?php
	$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");
	while ($row_module = db_fetch_array($res_module)) {
		print "<OPTION value=$row_module[filemodule_id]>$row_module[module_name]";
	}
?>
</SELECT>

<P>Text Display Format
<BR><INPUT type="checkbox" value="1" name="form_text_format">&nbsp;Display notes & changelog as text (PRE) instead of HTML.

<P>Release Notes
<BR><I>HTML is OK. Use HTML syntax for new paragraphs and new lines.</I>
<BR><TEXTAREA name=form_text_notes wrap=virtual rows=15 cols=80>
</TEXTAREA>

<P>Changelog
<BR><I>HTML is OK. Use HTML syntax for new paragraphs and new lines.</I>
<BR><TEXTAREA name=form_text_changes wrap=virtual rows=15 cols=80>
</TEXTAREA>

<INPUT type=hidden name="group_id" value="<?php print $group_id; ?>">
<INPUT type=hidden name="form_filetype" value="<?php print $form_filetype; ?>">
<INPUT type=hidden name="form_filesize" value="<?php 
	print filesize($FTPINCOMING_DIR . "/" . $form_filename); ?>">
<INPUT type=hidden name="form_filename" value="<?php 
	print $form_filename; ?>">
<P><INPUT type=submit name="Submit" value="Submit">

</FORM>

<?php
project_admin_footer(array());
?>
