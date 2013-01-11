<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: logout.php,v 1.6 2000/04/14 17:53:41 tperdue Exp $

require ('pre.php');    

//someday, we should delete the session from the database

session_cookie('session_hash','');
session_redirect('/');

?>
