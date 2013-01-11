<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: newprojectmail.php,v 1.8 2000/03/10 10:24:37 tperdue Exp $

require ('pre.php');
require('proj_email.php');

session_require(array('group'=>'1','admin_flags'=>'A'));

site_header(array('title'=>"Project Intro email"));

send_new_project_email($group_id);

print "<P>Mail successfully sent.";

site_footer(array());
?>
