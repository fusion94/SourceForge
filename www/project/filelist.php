<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: filelist.php,v 1.54 2000/05/04 22:19:22 dtype Exp $

require "pre.php";    
if ((!$group_id) && $form_grp) $group_id=$form_grp;

$res_module = db_query("SELECT * FROM filemodule WHERE group_id=$group_id");

if (db_numrows($res_module) < 1) {
	exit_error("No File Modules","There are no file modules defined for this project.");
}

site_header(array('title'=>'Project Filelist','group'=>$group_id));

// top bar
html_tabs('downloads',$group_id);

print '<P>';
?><TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<TR><TD><B>Filename</B></TD>
<TD align=right><B>Size&nbsp;&nbsp;</B></TD>
<TD align=right><B>D/L&nbsp;&nbsp;</B></TD>
<TD align=right><B>Version&nbsp;&nbsp;</B></TD>
<TD><B>Type</B></TD>
<TD><B>Released</B></TD>
<TD><B>View</B></TD></TR>
<?php

// get unix group name for path
$res_grp = db_query("SELECT unix_group_name FROM groups WHERE group_id=$group_id");
if (db_numrows($res_grp) < 1) exit_error("Invalid Group","Group ID is not valid.");
$row_grp = db_fetch_array($res_grp);

while ($row_module = db_fetch_array($res_module)) {
	$res_file = db_query("SELECT filerelease.filerelease_id AS filerelease_id,"
		."filerelease.user_id AS user_id,"
		."filerelease.release_version AS release_version,"
		."filerelease.filename AS filename,"
		."filerelease.file_type AS file_type,"
		."filerelease.release_time AS release_time,"
		."filerelease.group_id AS group_id,"
		."filerelease.file_size AS file_size,"
		."SUM(frs_dlstats_agg.downloads_http + frs_dlstats_agg.downloads_ftp) AS downloads "
		."FROM filerelease,frs_dlstats_agg WHERE "
		."frs_dlstats_agg.file_id=filerelease.filerelease_id AND "
		."filemodule_id=$row_module[filemodule_id] AND status='A' "
		."GROUP BY frs_dlstats_agg.file_id "
		."ORDER BY release_time DESC",1);
	print '<TR><TD colspan=7>&nbsp;<BR>File Module: <B>'.$row_module['module_name'].'</B></TD></TR>';
	while ($row_file = db_fetch_array($res_file)) {
		$i++;
		print '<TR BGCOLOR="'. util_get_alt_row_color($i) .'">';
		print '<TD><A href="http://download.sourceforge.net/'
			.$row_grp['unix_group_name'].'/'.$row_file['filename'].'">';
		print $row_file['filename'].'</A></TD>';
		print "<TD align=right>$row_file[file_size]&nbsp;&nbsp;</TD>";
		print "<TD align=right>$row_file[downloads]&nbsp;&nbsp;</TD>";
		print "<TD align=right>$row_file[release_version]&nbsp;&nbsp;</TD>";
		print "<TD>$row_file[file_type]</TD>";
		print "<TD>" . date("m-d-Y",$row_file['release_time']) . "</TD>";
		print "<TD>";
		print "<A href=\"filenotes.php?group_id=$group_id&form_filemodule_id="
			. "$row_module[filemodule_id]&form_release_version="
			. urlencode($row_file['release_version']) . "\">[Notes]</A></TD>";
		print "</TR>\n";
		// add totals
		$total_files += 1;
		$total_bytes += $row_file['file_size'];
		$total_download += $row_file['downloads'];
	}
}

// print totals
print '<TR><TD colspan=7>&nbsp;<BR><B>Totals for this Project</B></TD></TR>';
print '<TR><TD>Files: <B>'.$total_files.'</B></TD><TD align="right"><B>'.$total_bytes.'</B>&nbsp;&nbsp;</TD>'
	.'<TD align="right"><B>'.$total_download.'</B>&nbsp;&nbsp;</TD><TD colspan=4>&nbsp;</TD></TR>'."\n";
print "</TABLE>";

site_footer(array());
?>
