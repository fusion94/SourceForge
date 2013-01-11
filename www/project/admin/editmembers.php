<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editmembers.php,v 1.6 2000/01/13 18:36:36 precision Exp $

require "pre.php";    

//FIXED
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

site_header(array(title=>"Edit Group Membership"));
?>



<?php
site_footer(array());
site_cleanup(array());
?>
