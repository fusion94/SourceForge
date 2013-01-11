<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: memberlist.php,v 1.12 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
if ((!$group_id) && $form_grp) $group_id=$form_grp;
site_header(array(title=>"Project Member List",group=>$group_id));

// default to Enlightenment
if (!$group_id) $group_id = 2;

html_tabs("home",$group_id);

?>
<P>Developer list for project: <B><?php html_a_group($group_id); ?></B>

<P>If you would like to contribute to this project by becoming a developer,
contact one of the project admins, designated in bold text below.

<BR>&nbsp;
<?php

// list members
$res_memb = db_query("SELECT user.user_name AS user_name,user.user_id AS user_id,"
	. "user_group.admin_flags AS admin_flags FROM user,user_group WHERE "
	. "user.user_id=user_group.user_id AND user_group.group_id=$group_id "
	. "ORDER BY user.user_name");

while ($row_memb=db_fetch_array($res_memb)) {
	print "<BR>";
	if ($row_memb[admin_flags]=='A') print "<B>";
	print "<A href=\"/developer/index.php?form_dev=$row_memb[user_id]\">$row_memb[user_name]</A>";
	if ($row_memb[admin_flags]=='A') print "</B>";
}

site_footer(array());
site_cleanup(array());
?>
