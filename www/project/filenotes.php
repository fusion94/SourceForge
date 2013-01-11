<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: filenotes.php,v 1.11 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
if ((!$group_id) && $form_grp) $group_id=$form_grp;

$res_notes = db_query("SELECT file_type,text_notes,text_changes,text_format FROM filerelease WHERE "
	. "filemodule_id=$form_filemodule_id AND release_version='$form_release_version'");

if (db_numrows($res_notes) < 1) {
	exit_error("Version does not exist","This file module/version does not exist."
		. "<BR>filemodule_id=$form_filemodule_id"
		. "<BR>release_version=$form_release_version");
}

site_header(array(title=>"File Release Notes and Changelog",group=>$group_id));

html_tabs("downloads",$group_id);

while ($row_notes = db_fetch_array($res_notes)) {
	if ($row_notes[text_format]) {
		$pre = "<PRE>";
		$post = "</PRE>";
	} else {
		$pre = "";
		$post = "";
	}
	html_box1_top("$form_release_version: $row_notes[file_type]");
	print "&nbsp;<BR><B>Release Notes:</B><P>$pre" . stripslashes($row_notes[text_notes]) . "$post";
	print "<HR>&nbsp;<BR><B>Changelog:</B><P>$pre" . stripslashes($row_notes[text_changes]) . "$post";
	html_box1_bottom();
}

site_footer(array());
site_cleanup(array());
?>
