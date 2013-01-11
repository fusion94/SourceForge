<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: menu.php,v 1.109 2000/05/03 11:23:45 tperdue Exp $


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
	<BR>
	<INPUT TYPE="CHECKBOX" NAME="exact" VALUE="1" CHECKED> Require All Words
	<BR>
	<INPUT TYPE="HIDDEN" VALUE="<?php echo $forum_id; ?>" NAME="forum_id">
	<INPUT TYPE="HIDDEN" VALUE="<?php echo $is_bug_page; ?>" NAME="is_bug_page">
	<INPUT TYPE="HIDDEN" VALUE="<?php echo $group_id; ?>" NAME="group_id">
	<INPUT TYPE="text" SIZE="15" NAME="words" VALUE="<?php echo $words; ?>">
	<BR>
	<INPUT TYPE="submit" NAME="Search" VALUE="Search">
	</FORM>
	<?php
}

function menuhtml_top($title) {
	/*
		Use only for the top most menu
	*/
	?>

	<!-- menuhtml_topmain() -->
	<table cellspacing="0" cellpadding="3" width="100%" border="0" bgcolor="<?php echo $GLOBALS['COLOR_MENUBARBACK']; ?>">
	<tr bgcolor="<?php echo $GLOBALS['COLOR_MENUBARBACK']; ?>">
	<td align="center">
	<?php html_blankimage(1,135); ?><BR>
	<span class="titlebar"><font color="#ffffff"><?php print $title; ?></font></span></td>
	</tr>
	<tr align="right" BGCOLOR="<?php echo $GLOBALS['COLOR_MENUBACK']; ?>"><td>
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
	menuhtml_top('Software'); 
	print '
		<A class="menus" href="/softwaremap/">Software Map</A>
		<BR><A class="menus" href="/new/">New Releases</a>
		<BR><A class="menus" href="/mirrors/">Other Site Mirrors</A>
		<BR><A class="menus" href="/snippet/">Code Snippet Library</A>';
	menuhtml_bottom();
	menuhtml_top('SourceForge');
	print '
		<A class="menus" href="/docs/site/">Site Documentation</A>
		<BR><A class="menus" href="/support/?func=addsupport&group_id=1">Request Support</A>
		<BR><A class="menus" href="/forum/forum.php?forum_id=3">Help Forum</A>
		<BR><A class="menus" href="/forum/forum.php?forum_id=2">Discussion Forum</A>
		<BR><A class="menus" href="/people/">Project Help Wanted</A>
		<P>
		<A class="menus" href="/forum/forum.php?forum_id=4">Feature Request</A>
		<BR><A class="menus" href="/bugs/?group_id=1">Report SF Bug</A>
		<BR><A class="menus" href="/patch/?group_id=1">Submit SF Patch</A>

		<BR><A class="menus" href="/top/">Top Projects</A>';
	menuhtml_bottom();
}

function menu_search() {
	menuhtml_top('Search');
	menu_show_search_box();
	menuhtml_bottom();
}

function menu_project($grp) {
	menuhtml_top('Project: ' . group_getname($grp));
	print '
		<A class=menus href="/project/?group_id='.$grp.'">Project Summary</A>
                <P><A class=menus href="/project/admin/?group_id='.$grp.'">Project Admin</A>
                <BR><A class=menus href="/project/admin/addfile.php?group_id='.$grp.'">File Release</A>
                <BR><A class=menus href="/project/admin/userperms.php?group_id='.$grp.'">User Permissions</A>';
	menuhtml_bottom();
}

function menu_projectadmin($grp) {
	menuhtml_top('Project Administrator');
	print '
		<I>(<A class=menus href="/project/?group_id='.$grp.'">Project Summary</A>)</I>
		<BR>&nbsp;<BR><A class=menus href="/project/admin/?group_id='.$grp.'">Project Admin</A>
		<BR><A class=menus href="/project/admin/addfile.php?group_id='.$grp.'">File Release</A>
		<BR><A class=menus href="/project/admin/userperms.php?group_id='.$grp.'">User Permissions</A>';
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
