<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mod_filters.php,v 1.11 2000/01/13 18:36:34 precision Exp $

bug_header(array ("title"=>"Create a Personal Filter"));

if (user_isloggedin()) {

	echo "<H2>Create a personal filter for ".user_getname()."</H2>";
	echo "<B>Creating or modifying a filter makes it your active filter</B><P>";
	echo "Be sure include 'bug.' before each field name, as in the example, as multiple tables are being joined in the query";

	show_filters($group_id);

	$sql="SELECT user.user_id,user.user_name FROM user,user_group WHERE user.user_id=user_group.user_id AND user_group.bug_flags IN (1,2) AND user_group.group_id='$group_id'";
	$result=db_query($sql);

	$sql="select * from bug_status";
	$result2=db_query($sql);

	$sql="select bug_category_id,category_name from bug_category WHERE group_id='$group_id'";
	$result3=db_query($sql);

	$sql="select * from bug_resolution";
	$result4=db_query($sql);

	$sql="select bug_group_id,group_name from bug_group WHERE group_id='$group_id'";
	$result5=db_query($sql);

	?>
	<TABLE WIDTH="100%" CELLPADDING="3">
		<TR>
			<TD  COLSPAN="3">
				<B>The following tables show which statuses, technicians, and categories you can include in your filter.
			</TD>
		</TR>
		<TR>
			<TD  VALIGN="TOP"><?php ShowResultSet($result,"Bug Techs for ".group_getname($group_id)); ?></TD>
			<TD  VALIGN="TOP"><?php ShowResultSet($result2,"Bug Statuses"); ?></TD>
			<TD  VALIGN="TOP"><?php ShowResultSet($result3,"Bug Categories for ".group_getname($group_id)); ?></TD>
		<TR>
		<TR>
			<TD  VALIGN="TOP"><?php ShowResultSet($result4,"Bug Resolutions"); ?></TD>
			<TD  VALIGN="TOP"><?php ShowResultSet($result5,"Bug Groups"); ?></TD>
			<TD>&nbsp;</TD>
		</TR>
	</TABLE>
	<?php

} else {

	echo "<H1>You must be logged in before you can create personal filters for any given group</H2>";

}

bug_footer(array());

?>
