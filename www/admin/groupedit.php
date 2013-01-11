<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: groupedit.php,v 1.59 2000/03/07 13:20:24 tperdue Exp $

require "pre.php";    
require "vars.php";

session_require(array('group'=>'1','admin_flags'=>'A'));

// group remove
if ($group_idrm) {
	db_query("DELETE FROM group_category WHERE group_id='$group_idrm' "
		. "AND category_id='$form_catrm'");
}

// group public choice
if ($Update) {
   db_query("UPDATE groups SET public=$form_public,status='$form_status',"
	. "license='$form_license',"
	. "unix_box='$form_box',http_domain='$form_domain' WHERE group_id=$group_id");
}

// get current information
$res_grp = db_query("SELECT * FROM groups WHERE group_id=$group_id");

if (db_numrows($res_grp) < 1) {
	exit_error("Invalid Group","Invalid group was passed in.");
}

$row_grp = db_fetch_array($res_grp);

site_header(array('title'=>"Editing Group"));

echo '<H2>'.$row_grp['group_name'].'</H2>' ;?>

<p>
<?php print "<A href=\"/project/admin/?group_id=$group_id\"><H3>[Project Admin]</H3></A>"; ?></b>

<P>
<A href="userlist.php?group_id=<?php print $group_id; ?>"><H3>[View/Edit Group Members]</H3></A>

<p>
<FORM action="<?php echo $PHP_SELF; ?>" method="POST">

<B>Status</B>
<SELECT name="form_status">
<OPTION <?php if ($row_grp['status'] == "I") print "selected "; ?> value="I">Incomplete</OPTION>
<OPTION <?php if ($row_grp['status'] == "A") print "selected "; ?> value="A">Active
<OPTION <?php if ($row_grp['status'] == "P") print "selected "; ?> value="P">Pending
<OPTION <?php if ($row_grp['status'] == "H") print "selected "; ?> value="H">Holding
<OPTION <?php if ($row_grp['status'] == "D") print "selected "; ?> value="D">Deleted
</SELECT>

<B>Public?</B>
<SELECT name="form_public">
<OPTION <?php if ($row_grp['public'] == 1) print "selected "; ?> value="1">Yes
<OPTION <?php if ($row_grp['public'] == 0) print "selected "; ?> value="0">No
</SELECT>

<P><B>License</B>
<SELECT name="form_license">
<OPTION value="none">N/A
<OPTION value="other">Other
<?php
	while (list($k,$v) = each($LICENSE)) {
		print "<OPTION value=\"$k\"";
		if ($k == $row_grp['license']) print " selected";
		print ">$v\n";
	}
?>
</SELECT>

<INPUT type="hidden" name="group_id" value="<?php print $group_id; ?>">
<BR>Home Box: <INPUT type="text" name="form_box" value="<?php print $row_grp['unix_box']; ?>">
<BR>HTTP Domain: <INPUT size=40 type="text" name="form_domain" value="<?php print $row_grp['http_domain']; ?>">
<BR><INPUT type="submit" name="Update" value="Update">
</FORM>

<P><A href="newprojectmail.php?group_id=<?php print $group_id; ?>">Send New Project Instruction Email</A>

<HR>
<p><b>Categories</b>
<a href="groupedit-newcat.php?group_id=<?php print $group_id; ?>">[New Category Association]</a>
<br>&nbsp;
<?php
$res_cat = db_query("SELECT category.category_id AS category_id,"
	. "category.category_name AS category_name FROM category,group_category "
	. "WHERE category.category_id=group_category.category_id AND "
	. "group_category.group_id=$group_id");
while ($row_cat = db_fetch_array($res_cat)) {
	print "<br>$row_cat[category_name] "
         . "<A href=\"groupedit.php?group_id=$group_id&group_idrm=$group_id&form_catrm=$row_cat[category_id]\">"
         . "[Remove from Category]</A>";
}

// ########################## OTHER INFO

print "<HR><P><B>Other Information</B>";
print "<P>Unix Group Name: $row_grp[unix_group_name]";

print "<P>Submitted Description:<P> $row_grp[register_purpose]";

print "<P>License Other: <P> $row_grp[license_other]";

site_footer(array());
site_cleanup(array());
?>
