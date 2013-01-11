<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: index.php,v 1.96 2000/01/13 18:36:36 precision Exp $

require "pre.php";    
require "vars.php";
site_header(array(title=>"Software Map"));

// ********* function sfm_getenvquery();

function sfm_getenvquery() {
	if (!$GLOBALS[limitenv]) return '';
	$expl_env = explode('-',$GLOBALS[limitenv]);
	if (!$expl_env[0]) return '';
	$return = ' AND (';
	for ($i=0; $i<sizeof($expl_env); $i++) {
		if ($i>0) $return .= ' OR';
		$return .= " group_env.env_id='$expl_env[$i]'";
	}
	$return .= ') ';
	return $return;
}

// ****************** END FUNCTION

// assign default
if (!$form_cat) $form_cat = 3;
print '<P>';

// ######## two column table for key on right
print '<TABLE width=100% border="0" cellspacing="0" cellpadding="0">
<TR valign="top"><TD width="50%">';
$folders = explode("::",category_tree($form_cat,0,0));
$folders_len = count($folders);
for ($i=0;$i<$folders_len;$i++) {
	for ($sp=0;$sp<($i*2);$sp++) {
		print " &nbsp; ";
	}
	// no anchor for last one
	if ($i != ($folders_len-1)) print "<A href=\"/softwaremap/?form_cat=$folders[$i]\">";
	html_image("ic/ofolder15.png",array());
	print "&nbsp; ";
	// get category information
	$res_cat = db_query("SELECT category_name,sub_files FROM category WHERE category_id=$folders[$i]");
	$row_cat = db_fetch_array($res_cat);
	print "$row_cat[category_name]";
	// no anchor for last one	
	if ($i != ($folders_len-1)) print "</A>";
	print " ($row_cat[sub_files])";
	print "<BR>\n";
}

$res_sub = db_query("SELECT category.category_id AS category_id,"
	. "category.sub_files AS sub_files,"
	. "category.category_name AS category_name FROM category,category_link WHERE "
	. "category.category_id=category_link.child AND category_link.parent=$form_cat "
	. "ORDER BY category.category_name");
while ($row_sub = db_fetch_array($res_sub)) {
	for ($sp=0;$sp<($folders_len*2);$sp++) {
		print " &nbsp; ";
	}
	print "<a href=\"index.php?form_cat=$row_sub[category_id]\">";
	html_image("ic/cfolder15.png",array());
	print "&nbsp; $row_sub[category_name]</a> ($row_sub[sub_files])<BR>";
}
// ########### right column: KEY!
print '</TD><TD width="50%">
<B>Key to Software Environments</B>
<BR>&nbsp;<TABLE cellspacing="0" cellpadding="0" border="0">';
for ($i=1;$i<=8;$i++) {
	print '<TR><TD>';
	if ($ENVLINK[$i]) print '<A href="'.$ENVLINK[$i].'">';
	print html_image('ic/'.$ENVFILE[$i],array(),0);
	if ($ENVLINK[$i]) print '</A>';
	print '&nbsp;</TD><TD>'.$SOFTENV[$i]."</TD></TR>\n";
}
print '</TABLE></TD></TR></TABLE>';
?>
<HR noshade>
<?php
// one listing for each project
$res_grp = db_query("SELECT groups.group_id AS group_id, "
	. "groups.group_name AS group_name, "
	. "groups.status AS status, "
	. "groups.short_description AS short_description "
	. "FROM groups,group_category,group_env WHERE "
	. "group_category.group_id=groups.group_id AND "
	. "groups.public=1 AND "
	. "(groups.status='A') AND "
	. "group_category.category_id=$form_cat AND "
	. "groups.group_id=group_env.group_id "
	. sfm_getenvquery()
	. "GROUP BY groups.group_id ORDER BY groups.group_name");
if (db_numrows($res_grp) < 1) {
	print "<I>There are no projects in this category.</I>";
}
while ($row_grp = db_fetch_array($res_grp)) {
	print '<SPAN>';
	print "<a href=\"/project/?group_id=$row_grp[group_id]\"><B>"
		.htmlspecialchars(stripslashes($row_grp[group_name]))."</B></a> ";
	if ($row_grp[short_description]) {
		print "- " . htmlspecialchars(stripslashes($row_grp[short_description]));
	}
	print ' <I>'.html_displaylanguages($row_grp[group_id]).'</I>';
	print '</SPAN><BR><SPAN class="alignright">';
	print html_displayenvironments($row_grp[group_id]);
	print "</SPAN>\n";
	print '<HR>';
}
?>

<?php
site_footer(array());
site_cleanup(array());
?>
