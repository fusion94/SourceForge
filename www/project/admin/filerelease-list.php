<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: filerelease-list.php,v 1.13 2000/07/03 15:31:21 tperdue Exp $

require "pre.php";    
require ($DOCUMENT_ROOT.'/project/admin/project_admin_utils.php');

session_require(array('group'=>$group_id,'admin_flags'=>'A'));

project_admin_header(array('title'=>'Admin Project Filelist','group'=>$group_id));

$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");

if (db_numrows($res_module) < 1) {
	exit_error("No File Modules","There are no file modules defined for this project.");
}

while ($row_module = db_fetch_array($res_module)) {
	html_box1_top("File Module: $row_module[module_name]");
	$res_file = db_query("SELECT filerelease_id,user_id,release_version,filename,file_type,"
		. "release_time,downloads,status FROM filerelease WHERE "
		. "filemodule_id=$row_module[filemodule_id] ORDER BY release_version DESC");
?><TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR><TD>&nbsp;</TD>
<TD><B>Filename</B></TD>
<TD><B>Version</B></TD>
<TD><B>File Type</B></TD>
<TD><B>Release Date</B></TD></TR>
<?php
	while ($row_file = db_fetch_array($res_file)) {
		$i++;
		print "<TR BGCOLOR=\"". util_get_alt_row_color($i) ."\"><TD rowspan=2><A href=\"filerelease-edit.php?group_id=$group_id"
			. "&form_filerelease_id=$row_file[filerelease_id]\">[Edit]</A></TD>";
		print "<TD>$row_file[filename]</TD>";
		print "<TD>$row_file[release_version]</TD>";
		print "<TD>$row_file[file_type]</TD>";
		print "<TD>" . date($sys_datefmt,$row_file['release_time']) . "</TD>";
		print "</TR>\n";
		print "<TR BGCOLOR=\"". util_get_alt_row_color($i) ."\"><TD colspan=3>";
		print "<A href=\"../filenotes.php?group_id=$group_id&form_filemodule_id="
			. "$row_module[filemodule_id]&form_release_version="
			. urlencode($row_file['release_version']) . "\">View Notes & Changelog</A></TD>";
		print "<TD>by ";
		print '<A href="/developer/?form_dev='.$user.'">'.user_getname( $row_file['user_id'] ).'</A>';
		print "</TD>";
		print "</TR>\n";
		// print additional information for new or changed files
		if (strcmp($row_file['status'],'A')) {
			if ($row_file['status'] == 'N') {
				$i++;
				print '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD colspan="5"><FONT color="#FF0000">'
					.'This entry is new and will be copied to the main file archive within 120 seconds.'
					.'</FONT></TD></TR>';
			}
			if ($row_file['status'] == 'D') {
				$i++;
				print '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD colspan="5"><FONT color="#FF0000">'
					.'This entry is deleted and not available for download.'
					.'</FONT></TD></TR>';
			}
			if ($row_file['status'] == 'E') {
				$i++;
				print '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD colspan="5"><FONT color="#FF0000">'
					.'This entry has been marked for deletion and will be archived within 120 seconds.'
					.'</FONT></TD></TR>';
			}
			if ($row_file['status'] == 'M') {
				$i++;
				print '<TR BGCOLOR="'. util_get_alt_row_color($i) .'"><TD colspan="5"><FONT color="#FF0000">'
					.'This entry has been modified and will be changed in the archive within 120 seconds.'
					.'</FONT></TD></TR>';
			}
		}
	}
	print "</TABLE>\n";
	html_box1_bottom();
}

project_admin_footer(array());
?>
