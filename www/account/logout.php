<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: logout.php,v 1.5 2000/01/13 18:36:34 precision Exp $

require "pre.php";    

session_set_new();
session_redirect("/index.php");

?>
