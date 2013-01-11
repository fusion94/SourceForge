<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: home.php,v 1.3 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

$expl_pathinfo = explode('/',$GLOBALS[PATH_INFO]);

$res_grp = db_query("SELECT groups.group_id AS group_id FROM groups WHERE "
	."unix_group_name='$expl_pathinfo[1]'");
if (db_numrows($res_grp) < 1) exit_error("Invalid Group","That group does not exist.");
$row_grp = db_fetch_array($res_grp);

session_redirect('/project/?group_id='.$row_grp[group_id]);

print ("\n\n");
exit;
?>
