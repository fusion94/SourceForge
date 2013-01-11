<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: download.php,v 1.11 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

$expl_pathinfo = explode('/',$GLOBALS[PATH_INFO]);

// ******* case 1: original. file_id is passed
if ($fileid) {
	$res_file = db_query("SELECT groups.unix_group_name AS unix_group_name,"
		. "filerelease.filename AS filename "
		. "FROM groups,filerelease WHERE groups.group_id=filerelease.group_id AND "
		. "filerelease.filerelease_id=$fileid");
	if (db_numrows($res_file) < 1) exit_error("Invalid File ID","That file does not exist.");
	$row_file = db_fetch_array($res_file);
	$header = ("Location: http://download.sourceforge.net/" . $row_file[unix_group_name] . "/"
		. $row_file[filename]);

}
// ******* case 2: Group name only is called
else if ($expl_pathinfo[1] && !$expl_pathinfo[2]) {
	$res_grp = db_query("SELECT group_id FROM groups WHERE unix_group_name='$expl_pathinfo[1]'");
	if (db_numrows($res_grp) < 1) exit_error("Unknown Group","Unable to resolve group name.");
	$row_grp = db_fetch_array($res_grp);
	$header = 'Location: http://'.getenv('HTTP_HOST').'/project/filelist.php?group_id='
		.$row_grp[group_id];
}
// ******* case 3: Group name and filename exists
else if ($expl_pathinfo[1] && $expl_pathinfo[2]) {
	$res_file = db_query("SELECT filerelease.filerelease_id AS filerelease_id FROM filerelease,groups "
		."WHERE groups.group_id=filerelease.group_id AND "
		."filerelease.filename='$expl_pathinfo[2]' AND "
		."groups.unix_group_name='$expl_pathinfo[1]'");
	if (db_numrows($res_file) < 1) exit_error("Unknown file","That file does not exist.");
	$row_file = db_fetch_array($res_file);
	$header = ("Location: http://download.sourceforge.net/".$expl_pathinfo[1]
		."/".$expl_pathinfo[2]);
	$fileid = $row_file[filerelease_id];
}
// *******
else {
	exit_error("Unknown file","This URL was called in an ambiguous way. I am unable to "
		."determine the download file.<P>Path Info: $GLOBALS[PATH_INFO]");
}

// log it
if ($fileid) db_query("INSERT INTO filedownload_log (user_id,filerelease_id,time) VALUES ("
	. user_getid() . ","
	. $fileid . ","
	. time() . ")");

header($header);

print ("\n\n");
exit;
?>
