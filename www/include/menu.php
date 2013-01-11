<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: menu.php,v 1.100 2000/01/13 18:36:35 precision Exp $


function menu_show_search_box() {
	global $words,$forum_id,$group_id,$is_bug_page;
	?>
	<CENTER>
	<FONT SIZE="-2">
	<FORM action="/search/" method=post>
	<SELECT name="type_of_search">
	<?php
	if ($is_bug_page && $group_id) {
		echo '
			<OPTION value="bugs">Bugs</OPTION>';
	} else if ($group_id && $forum_id) {
		echo '
			<OPTION value="forums">This Forum</OPTION>';
	}
	?>
	<OPTION value="soft">Software/Group</OPTION>
	<OPTION value="people">People</OPTION>
	</SELECT>
	<INPUT TYPE="HIDDEN" VALUE="<?php echo $forum_id; ?>" NAME="forum_id">
	<INPUT TYPE="HIDDEN" VALUE="<?php echo $group_id; ?>" NAME="group_id">
	<BR><INPUT TYPE="text" SIZE="15" NAME="words" VALUE="<?php echo $words; ?>">
	<BR><INPUT TYPE="submit" NAME="Search" VALUE="Search">
	</FORM>
	<?php
}

function menuhtml_top($title) {
	/*
		Use only for the top most menu
	*/
	?>

	<!-- menuhtml_topmain() -->
	<table cellspacing="0" cellpadding="3" width="100%" border="0" bgcolor="<?php echo $GLOBALS[COLOR_MENUBARBACK]; ?>">
	<tr bgcolor="<?php echo $GLOBALS[COLOR_MENUBARBACK]; ?>">
	<td align="center">
	<?php html_blankimage(1,135); ?><BR>
	<span class="titlebar"><font color="#ffffff"><?php print $title; ?></font></span></td>
	</tr>
	<tr align="right" BGCOLOR="<?php echo $GLOBALS[COLOR_MENUBACK]; ?>"><td>
	<!-- end -->

	<?php
}

function menuhtml_bottom() {
	/*
		End the table
	*/
	print '

		</TD>
		<!-- menuhtml_bottom() -->
		</TR></TABLE>
		<!-- end -->
';
}

function menu_main() {
	menuhtml_top('SourceForge'); 
	print '
		<A class="menus" href="/">Homepage</A>
		<BR><A class="menus" href="/snippet/">Code Snippet Library</A>
		<BR><A class="menus" href="/softwaremap/">Software Map</A>
		<BR><A class="menus" href="/new/">New Releases</a>
		<BR><A class="menus" href="/docs/site/">Site Documentation</A>
		<BR><A class="menus" href="/top/">Top Projects</A>';
	menuhtml_bottom();
}

function menu_admin() {
	menuhtml_top('Site Administrator');
	print '
		<A class=menus href="/admin/">Site Admin</A>
		<BR><A class=menus href="/cgi-bin/cvsweb.cgi">Prodigy CVS Tree</A>';
	menuhtml_bottom();
}

function menu_search() {
	menuhtml_top('Search');
	menu_show_search_box();
	menuhtml_bottom();
}

function menu_isgroupactive($group) {
	if (isset($GLOBALS[G_GROUPACTIVE])) {
		return $GLOBALS[G_GROUPACTIVE];
	}

	$res_active = db_query('SELECT status FROM groups WHERE group_id='.$group);
	$row_active = db_fetch_array($res_active);
	$GLOBALS[G_GROUPACTIVE] = ($row_active[status] == 'A');
	return $GLOBALS[G_GROUPACTIVE];
}

function menu_project($grp) {
	menuhtml_top('Project: ' . group_getname($grp));
	print '
		<BR><A class=menus href="/project/?group_id='.$grp.'">Project Summary</A>
		<BR><A class=menus href="/forum/?group_id='.$grp.'">Message Forums</A>';
	if (menu_isgroupactive($grp)) print '<BR><A class=menus href="/bugs/?group_id='.$grp.'">Bug Tracking</A>';
	if (menu_isgroupactive($grp)) print '<BR><A class=menus href="/survey/?group_id='.$grp.'">Surveys</A>';
	if (menu_isgroupactive($grp)) print '<BR><A class=menus href="/mail/?group_id='.$grp.'">Mailing Lists</A>';
	menuhtml_bottom();
}

function menu_projectadmin($grp) {
	menuhtml_top('Project Administrator');
	print '
		<I>(<A class=menus href="/project/?group_id='.$grp.'">'.group_getname($grp).'</A>)</I>
		<BR>&nbsp;<BR><A class=menus href="/project/admin/?group_id='.$grp.'">Project Admin</A>
		<BR><A class=menus href="/project/admin/addfile.php?group_id='.$grp.'">File Release</A>
		<BR><A class=menus href="/project/admin/userperms.php?group_id='.$grp.'">User Permissions</A>
		<BR><A class=menus href="/bugs/admin/?group_id='.$grp.'">Bug Admin</A>
		<BR><A class=menus href="/survey/admin/?group_id='.$grp.'">Survey Admin</A>
		<BR><A class=menus href="/forum/admin/?group_id='.$grp.'">Forum Admin</A>
		<BR><A class=menus href="/mail/admin/?group_id='.$grp.'">Mailing List Admin</A>
		<BR><A class=menus href="/pm/admin/?group_id='.$grp.'">Task Manager Admin</A>';
	menuhtml_bottom();
}

function menu_projectdevel($grp) {
	menuhtml_top('Project Developer');
	print '
		<I>(<A class=menus href="/project/?group_id='.$grp.'">'.group_getname($grp).'</A>)</I>
		<BR>&nbsp;<BR>
		<A class=menus href="/pm/?group_id='.$grp.'">Task Manager</A>
		<BR>
		<A class=menus href="/bugs/?group_id='.$grp.'">Bug Tracking</A>';
	menuhtml_bottom();
}

function menu_loggedin() {
	menuhtml_top('Logged In: '.user_getname());
	print '
		<A class=menus href="/account/logout.php">Logout</A>
		<BR>
		<A class=menus href="/register/">Register New Project</A>
		<BR>
		<A class=menus href="/account/">Account Maintenance</A>
		<BR>
		<A class=menus href="/my/">My Personal Page</A>';
	menuhtml_bottom();
}

function menu_notloggedin() {
	menuhtml_top('Not Logged In');
	print '
		<A class=menus href="https://' . getenv('HTTP_HOST').'/account/login.php">Login via SSL</A>
		<BR><A class=menus href="https://'.getenv('HTTP_HOST').'/account/register.php">New User via SSL</A>';
	menuhtml_bottom();
}

?>
