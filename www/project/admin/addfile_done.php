<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: addfile_done.php,v 1.51 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
session_require(array(group=>$group_id,admin_flags=>"A"));
// error checking

if (!$form_release_version) exit_error("Submit Error","You must enter a release version number.");
if (!$form_filemodule) exit_error("Submit Error","You must define and select a filemodule.");

// date field
if (!$form_release_time) {
	$unix_release_time = time();
} elseif (!ereg("[0-9]{4}-[0-9]{2}-[0-9]{2}",$form_release_time)) {
	exit_error("Invalid Date Format","Date entry could not be parsed.");
} else { //is valid date... parse it
	$date_list = split("-",$form_release_time,3);
	$unix_release_time = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
}

// make sure not submitting files in the future

if ($unix_release_time > time()) {
	$unix_release_time = time();
}

// add to filerelease

db_query("INSERT INTO filerelease (group_id,user_id,unix_box,unix_partition,text_notes,text_changes,"
	. "release_version,filename,filemodule_id,file_type,release_time,file_size,post_time,text_format,status) VALUES "
	. "($group_id," . user_getid() . ",'remission',3,'$form_text_notes','$form_text_changes',"
	. "'$form_release_version','$form_filename',$form_filemodule,'$form_filetype',$unix_release_time,"
	. "$form_filesize," . time() . ",'$form_text_format','N')");

// make this the most recent release in module

db_query("UPDATE filemodule SET recent_filerelease='$form_release_version' WHERE "
	. "filemodule_id=$form_filemodule");

site_header(array(title=>"File Release Confirmation",group=>$group_id));
?>

<P><B>Success!</B>

<P>This file has been successfully added to the SourceForge repository. It will be available
for download within 120 seconds.

<P><A href="/project/?group_id=<?php print $group_id; ?>">[Return to Project Page]</A>
<BR><A href="/project/admin/?group_id=<?php print $group_id; ?>">[Return to Project Administration]</A>

<?php
site_footer(array());
site_cleanup(array());
?>
