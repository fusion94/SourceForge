<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: editmembers.php,v 1.7 2000/05/17 21:51:55 tperdue Exp $

require "pre.php";    

//FIXED
session_require(array('group'=>$group_id,'admin_flags'=>'A'));

site_header(array(title=>"Edit Group Membership"));
?>



<?php
site_footer(array());

?>
