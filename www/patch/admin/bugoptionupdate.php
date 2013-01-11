<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: bugoptionupdate.php,v 1.1 2000/01/18 03:06:47 tperdue Exp $

require "pre.php";    
session_require(array(group=>$group_id,admin_flags=>'A'));

db_query("UPDATE groups SET option_bugs=".($option_bugs?"1":"0")." WHERE group_id=$group_id");

session_redirect("/bugs/admin/?group_id=$group_id");
?>
