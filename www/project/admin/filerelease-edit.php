<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: filerelease-edit.php,v 1.26 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
require "paths.php";

session_require(array(group=>$group_id,adminflags=>"A"));

$res_file = db_query("SELECT * FROM filerelease WHERE filerelease_id=$form_filerelease_id");
if (db_numrows($res_file) < 1) {
	exit_error('Unknown file','That file does not exist.');
}
$row_file = db_fetch_array($res_file);

if ($row_file[group_id] != $group_id) {
	exit_error("Hack attempt","Don't try to edit another project's files, script kiddie."
		. " You've been logged. Don't bother running, you'll only die tired.");
}

if (($row_file[status] == 'M') || ($row_file[status] == 'E') || ($row_file[status] == 'N')) {
	exit_error("File Recently Modified","The system is still waiting for your last modification."
		." Please wait 120 seconds before additional modification.");
}

if ($GLOBALS[Submit]) {
	if (!$form_release_version || !$form_release_time || !$form_filemodule 
		|| !$form_status || !$form_filename) {
		exit_error("Error in submission","All the required fields were not filled in.");
	}

	if (!ereg("[0-9]{4}-[0-9]{2}-[0-9]{2}",$form_release_time)) {
        	exit_error("Invalid Date Format","Date entry could not be parsed.");
	} else { //is valid date... parse it
        	$date_list = split("-",$form_release_time,3);
        	$unix_release_time = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
	}

	// make sure not submitting in the future
	if ($unix_release_time > time()) {
		$unix_release_time = time();
	}

	// check for changed critical field
	if ( (strcmp($form_status,$row_file[status])) || (strcmp($form_filename,$row_file[filename]))  ) {
		// check for identical filename
		$res_samefile = db_query("SELECT filerelease_id FROM filerelease WHERE "
			."filename='$form_filename' AND group_id=$row_file[group_id] "
			."AND filerelease_id != $form_filerelease_id");
		if (db_numrows($res_samefile) > 0) {
			exit_error('Duplicate Filename','A file with that name already exists for your group.');
		}

		db_query('UPDATE filerelease SET '
			."status='".(($form_status=='D')?'E':'M')."',"
			."old_filename='$row_file[filename]',"
			."filename='$form_filename' WHERE filerelease_id=$form_filerelease_id");
	}

	db_query("UPDATE filerelease SET "
		. "release_version='$form_release_version',"
		. "release_time=$unix_release_time,"
		. "filemodule_id=$form_filemodule,"
		. "text_notes='$form_text_notes',"
		. "text_format='$form_text_format',"
		. "text_changes='$form_text_changes' WHERE filerelease_id=$form_filerelease_id");

	session_redirect("/project/admin/filerelease-list.php?group_id=$group_id");
}

site_header(array(title=>"File Release - Edit Information",group=>$group_id));
?>

<FORM action="filerelease-edit.php" method=post>
<P>Editing file: <B><?php print $row_file[filename]; ?></B>
<HR>
<P>Any changes made in this first block will take effect within 120 seconds
of submission via a cron job. 'Deleted' files are not actually removed, but
are archived and are not available for download. They can be 'undeleted' here
by changing their status. Deleted files cannot share a filename with active
files.

<?php
print '<P>File Status: <SELECT name="form_status">
<OPTION value="A"'.(($row_file[status]=='A')?' selected':'').'>Active
<OPTION value="D"'.(($row_file[status]=='D' || $row_file[status]=='E')?' selected':'').'>Deleted (Archived)
</SELECT>
';
print '<P>Filename
<BR><INPUT type="text" name="form_filename" value="'.$row_file[filename].'">
';
?>
<HR>
<P>Release Version Number
<BR><I>examples: 0.9.7, 1.0b4, 3.02, 1.0 pre4</I>
<BR><INPUT type=text name="form_release_version" value="<?php print $row_file[release_version]; ?>">

<P>Release Date
<BR><I>(YYYY-MM-DD) example: 1999-10-25. </I>
<BR><INPUT type=text name="form_release_time" value="<?php print date("Y-m-d",$row_file[release_time]); ?>">

<P>Module Name
<BR><I>Use the same module name for subsequent releases of the same product.
If no modules are defined, or you need to define a new one, visit 
<A href="/project/admin/?group_id=<?php print $group_id; ?>">project administration</A>
to define a module.</I>
<BR><SELECT name=form_filemodule>
<?php
	$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");
	while ($row_module = db_fetch_array($res_module)) {
		print "<OPTION value=$row_module[filemodule_id]";
		if ($row_file[filemodule_id] == $row_module[filemodule_id]) print " selected";
		print ">$row_module[module_name]";
	}
?>
</SELECT>

<P>Text Format
<BR><INPUT type="checkbox" value="1" name="form_text_format" <?php 
if ($row_file[text_format]) print " checked"; ?>>
Display notes and changelog as text (PRE) instead of HTML.

<P>Release Notes
<BR><I>HTML is OK. Use HTML syntax for new paragraphs and new lines.</I>
<BR><TEXTAREA name=form_text_notes wrap=virtual rows=15 cols=80>
<?php print stripslashes($row_file[text_notes]); ?>
</TEXTAREA>

<P>Changelog
<BR><I>HTML is OK. Use HTML syntax for new paragraphs and new lines.</I>
<BR><TEXTAREA name=form_text_changes wrap=virtual rows=15 cols=80>
<?php print stripslashes($row_file[text_changes]); ?>
</TEXTAREA>

<INPUT type=hidden name="group_id" value="<?php print $group_id; ?>">
<INPUT type=hidden name="form_filerelease_id" value="<?php print $form_filerelease_id; ?>">
<P><INPUT type=submit name="Submit" value="Submit">

</FORM>

<?php
site_footer(array());
site_cleanup(array());
?>
