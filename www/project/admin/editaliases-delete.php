<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editaliases-delete.php,v 1.3 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
require "account.php";

session_require(array(group=>$form_group,admin_flags=>'A'));

db_query("DELETE FROM mailaliases WHERE mailaliases_id=$form_mailid AND group_id=$group_id");

session_redirect("/project/admin/editaliases.php?group_id=$group_id");
?>
