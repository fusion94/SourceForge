<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: updateprefs.php,v 1.7 2000/01/13 18:36:34 precision Exp $

require "pre.php";    
session_require(array(isloggedin=>1));

db_query("UPDATE user SET "
	. "mail_siteupdates=" . ($form_mail_site?"1":"0") . ","
	. "mail_va=" . ($form_mail_va?"1":"0") . " WHERE "
	. "user_id=" . user_getid());

session_redirect("/account/");

?>
