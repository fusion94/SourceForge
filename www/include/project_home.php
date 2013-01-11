<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: project_home.php,v 1.92 2000/09/01 17:31:12 tperdue Exp $

require ('vote_function.php');
require ('vars.php');
require ($DOCUMENT_ROOT.'/news/news_utils.php');
require ('trove.php');

//make sure this project is NOT a foundry
if (!$project->isProject()) {
	header ("Location: /foundry/". $project->getUnixName() ."/");
	exit;
}       

$title = 'Project Info - '. $project->getPublicName();

site_project_header(array('title'=>$title,'group'=>$group_id,'toptab'=>'home'));


// ########################################### end top area

// two column deal
?>

<TABLE WIDTH="100%" BORDER="0">
<TR><TD WIDTH="99%" VALIGN="top">
<?php 

// ########################################## top area, not in box 
$res_admin = db_query("SELECT user.user_id AS user_id,user.user_name AS user_name "
	. "FROM user,user_group "
	. "WHERE user_group.user_id=user.user_id AND user_group.group_id=$group_id AND "
	. "user_group.admin_flags = 'A'");

if ($project->getStatus() == 'H') {
	print "<P>NOTE: This project entry is maintained by the SourceForge staff. We are not "
		. "the official site "
		. "for this product. Additional copyright information may be found on this project's homepage.\n";
}

if ($project->getDescription()) {
	print "<P>" . $project->getDescription();
} else {
	print "<P>This project has not yet submitted a description.";
}

// trove info
print '<BR>&nbsp;<BR>';
trove_getcatlisting($group_id,0,1);
print '<BR>&nbsp;';

print 'View project activity <a href="/project/stats/?group_id='.$group_id.'">statistics</a>';

print '</TD><TD NoWrap VALIGN="top">';

// ########################### Developers on this project

echo $HTML->box1_top("Developer Info");
?>
<?php
if (db_numrows($res_admin) > 0) {

	?>
	<SPAN CLASS="develtitle">Project Admins:</SPAN><BR>
	<?php
		while ($row_admin = db_fetch_array($res_admin)) {
			print "<A href=\"/users/$row_admin[user_name]/\">$row_admin[user_name]</A><BR>";
		}
	?>
	<HR WIDTH="100%" SIZE="1" NoShade>
	<?php

}

?>
<SPAN CLASS="develtitle">Developers:</SPAN><BR>
<?php
//count of developers on this project
$res_count = db_query("SELECT user_id FROM user_group WHERE group_id=$group_id");
print db_numrows($res_count);

?>

<A HREF="/project/memberlist.php?group_id=<?php print $group_id; ?>">[View Members]</A>
<?php 

echo $HTML->box1_bottom();

print '
</TD></TR>
</TABLE>
<P>
';


// ############################# File Releases

echo $HTML->box1_top('Latest File Releases'); 
	$unix_group_name = $project->getUnixName();

	echo '
	<TABLE cellspacing="1" cellpadding="5" width="100%" border="0">
		<TR bgcolor="'.$GLOBALS['COLOR_LTBACK1'].'">
		<TD align="left"">
			Package
		</td>
		<TD align="center">
			Version
		</td>
		<TD align="center">
			Notes / Monitor
		</td>
		<TD align="center">
			Download
		</td>
		</TR>';

		$sql="SELECT frs_package.package_id,frs_package.name AS package_name,frs_release.name AS release_name,frs_release.release_id AS release_id,frs_release.release_date AS release_date ".
			"FROM frs_package,frs_release ".
			"WHERE frs_package.package_id=frs_release.package_id ".
			"AND frs_package.group_id='$group_id' ".
			"AND frs_release.status_id=1 ".
			"ORDER BY frs_package.package_id,frs_release.release_date DESC";

		$res_files = db_query($sql);
		$rows_files=db_numrows($res_files);
		if (!$res_files || $rows_files < 1) {
			echo db_error();
			// No releases
			echo '<TR BGCOLOR="'.$GLOBALS['COLOR_LTBACK1'].'"><TD COLSPAN="4"><B>This Project Has Not Released Any Files</B></TD></TR>';

		} else {
			/*
				This query actually contains ALL releases of all packages
				We will test each row and make sure the package has changed before printing the row
			*/
			for ($f=0; $f<$rows_files; $f++) {
				if (db_result($res_files,$f,'package_id')==db_result($res_files,($f-1),'package_id')) {
					//same package as last iteration - don't show this release
				} else {
					echo '
					<TR BGCOLOR="'.$GLOBALS['COLOR_LTBACK1'].'" ALIGN="center">
					<TD ALIGN="left">
					<B>' . db_result($res_files,$f,'package_name'). '</B></TD>';
					// Releases to display
					print '<TD>'.db_result($res_files,$f,'release_name') .'
					</TD>
					<TD><A href="/project/shownotes.php?group_id=' . $group_id . '&release_id=' . db_result($res_files,$f,'release_id') . '">';
					echo	html_image("ic/manual16c.png",array('width'=>'15', 'height'=>'15', 'alt'=>'Release Notes'));
					echo '</A> - <A HREF="/project/filemodule_monitor.php?filemodule_id=' .	db_result($res_files,$f,'package_id') . '">';
					echo html_image("ic/mail16d.png",array('width'=>'15', 'height'=>'15', 'alt'=>'Monitor This Package'));
					echo '</A>
					</TD>
					<TD><A HREF="/project/showfiles.php?group_id=' . $group_id . '&release_id=' . db_result($res_files,$f,'release_id') . '">Download</A></TD></TR>';
				}
			}

		}
		?></TABLE>
	<div align="center">
	<a href="/project/showfiles.php?group_id=<?php print $group_id; ?>">[View ALL Project Files]</A>
	</div>
<?php
	echo $HTML->box1_bottom();

?>
<P>
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR><TD VALIGN="top">

<?php

// ############################## PUBLIC AREAS
echo $HTML->box1_top("Public Areas"); 

// ################# Homepage Link

print "<A href=\"http://" . $project->getHomePage() . "\">";
html_image("ic/home16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Homepage'));
print '&nbsp;Project Homepage</A>';

// ################## forums

if ($project->usesForum()) {
	print '<HR SIZE="1" NoShade><A href="/forum/?group_id='.$group_id.'">';
	html_image("ic/notes16.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Public Forums')); 
	print '&nbsp;Public Forums</A>';
	$res_count = db_query("SELECT count(forum.msg_id) AS count FROM forum,forum_group_list WHERE "
		. "forum_group_list.group_id=$group_id AND forum.group_forum_id=forum_group_list.group_forum_id "
		. "AND forum_group_list.is_public=1");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B> messages in ";

	$res_count = db_query("SELECT count(*) AS count FROM forum_group_list WHERE group_id=$group_id "
		. "AND is_public=1");
	$row_count = db_fetch_array($res_count);
	print "<B>$row_count[count]</B> forums )\n";
/*
	$sql="SELECT * FROM forum_group_list WHERE group_id='$group_id' AND is_public=1";
	$res2 = db_query ($sql);
	$rows = db_numrows($res2);
	for ($j = 0; $j < $rows; $j++) {
		echo '<BR> &nbsp; - <A HREF="forum.php?forum_id='.db_result($res2, $j, 'group_forum_id').'&et=0">'.
			db_result($res2, $j, 'forum_name').'</A> ';
		//message count
		echo '('.db_result(db_query("SELECT count(*) FROM forum WHERE group_forum_id='".db_result($res2, $j, 'group_forum_id')."'"),0,0).' msgs)';
	}
*/
}

// ##################### Bug tracking (only for Active)

if ($project->usesBugs()) {
	print '<HR SIZE="1" NoShade><A href="/bugs/?group_id='.$group_id.'">';
	html_image("ic/bug16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Bug Tracking')); 
	print '&nbsp;Bug Tracking</A>';
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id AND status_id != 3");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B>";
	$res_count = db_query("SELECT count(*) AS count FROM bug WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	print " open bugs, <B>$row_count[count]</B> total )";
}

// ##################### Support Manager (only for Active)
 
if ($project->usesSupport()) {
	print '
	<HR SIZE="1" NoShade>
	<A href="/support/?group_id='.$group_id.'">';
	html_image("ic/support16b.jpg",array('width'=>'20', 'height'=>'20', 'alt'=>'Support Manager'));
	print '&nbsp;Tech Support Manager</A>';
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id AND support_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open requests, <B>$row_count[count]</B> total )";
}

// ##################### Doc Manager (only for Active)

if ($project->usesDocman()) {
	print '
	<HR SIZE="1" NoShade>
	<A href="/docman/?group_id='.$group_id.'">';
	html_image("ic/docman16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Documentation'));
	print '&nbsp;DocManager: Project Documentation</A>';
/*
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM support WHERE group_id=$group_id AND support_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open requests, <B>$row_count[count]</B> total )";
*/
}

// ##################### Patch Manager (only for Active)

if ($project->usesPatch()) {
	print '
		<HR SIZE="1" NoShade>
		<A href="/patch/?group_id='.$group_id.'">';
	html_image("ic/patch.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Patch Manager'));
	print '&nbsp;Patch Manager</A>';
	$res_count = db_query("SELECT count(*) AS count FROM patch WHERE group_id=$group_id");
	$row_count = db_fetch_array($res_count);
	$res_count = db_query("SELECT count(*) AS count FROM patch WHERE group_id=$group_id AND patch_status_id='1'");
	$row_count2 = db_fetch_array($res_count);
	print " ( <B>$row_count2[count]</B>";
	print " open patches, <B>$row_count[count]</B> total )";
}

// ##################### Mailing lists (only for Active)

if ($project->usesMail()) {
	print '<HR SIZE="1" NoShade><A href="/mail/?group_id='.$group_id.'">';
	html_image("ic/mail16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Mailing Lists')); 
	print '&nbsp;Mailing Lists</A>';
	$res_count = db_query("SELECT count(*) AS count FROM mail_group_list WHERE group_id=$group_id AND is_public=1");
	$row_count = db_fetch_array($res_count);
	print " ( <B>$row_count[count]</B> public mailing lists )";
}

// ##################### Task Manager (only for Active)

if ($project->usesPm()) {
	print '<HR SIZE="1" NoShade><A href="/pm/?group_id='.$group_id.'">';
	html_image("ic/taskman16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Task Manager'));
	print '&nbsp;Project/Task Manager</A>';
	$sql="SELECT * FROM project_group_list WHERE group_id='$group_id' AND is_public=1";
	$result = db_query ($sql);
	$rows = db_numrows($result);
	if (!$result || $rows < 1) {
		echo '<BR><I>There are no public projects available</I>';
	} else {
		for ($j = 0; $j < $rows; $j++) {
			echo '
			<BR> &nbsp; - <A HREF="/pm/task.php?group_project_id='.db_result($result, $j, 'group_project_id').
			'&group_id='.$group_id.'&func=browse">'.db_result($result, $j, 'project_name').'</A>';
		}

	}
}

// ######################### Surveys (only for Active)

if ($project->usesSurvey()) {
	print '<HR SIZE="1" NoShade><A href="/survey/?group_id='.$group_id.'">';
	html_image("ic/survey16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Survey'));
	print " Surveys</A>";
	$sql="SELECT count(*) from surveys where group_id='$group_id' AND is_active='1'";
	$result=db_query($sql);
	echo ' ( <B>'.db_result($result,0,0).'</B> surveys )';
}

// ######################### CVS (only for Active)

if ($project->usesCVS()) {
	print '<HR SIZE="1" NoShade><A href="/cvs/?group_id='.$group_id.'">';
	html_image("ic/cvs16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'CVS'));
	print " CVS Repository</A>";
	$sql = "SELECT SUM(cvs_commits) AS commits,SUM(cvs_adds) AS adds from stats_project where group_id='$group_id'";
	$result = db_query($sql);
	echo ' ( <B>'.db_result($result,0,0).'</B> commits, <B>'.db_result($result,0,1).'</B> adds )';
}

// ######################## AnonFTP (only for Active)

if ($project->isActive()) {
	print '<HR SIZE="1" NoShade>';
	print "<A href=\"ftp://" . $project->getUnixName() . ".sourceforge.net/pub/". $project->getUnixName() ."/\">";
	print html_image("ic/ftp16b.png",array('width'=>'20', 'height'=>'20', 'alt'=>'Anonymous FTP Space'));
	print "Anonymous FTP Space</A>";
}

$HTML->box1_bottom();

if ($project->usesNews()) {
	// COLUMN BREAK
	?>

	</TD>
	<TD WIDTH="15">&nbsp;</TD>
	<TD VALIGN="top">

	<?php
	// ############################# Latest News

	echo $HTML->box1_top('Latest News');

	echo news_show_latest($group_id,10,false);

	echo $HTML->box1_bottom();
}

?>
</TD>

</TR></TABLE>

<?php

site_project_footer(array());

?>
