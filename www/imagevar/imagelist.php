<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: imagelist.php,v 1.4 2000/01/20 11:50:06 dtype Exp $

require "pre.php";    

if ($group_id) {
	$query = "SELECT image_id FROM image WHERE group_id='$group_id'";
} else if ($org_id) {
	$query = "SELECT image_id FROM image WHERE org_id='$org_id'";
} else {
	exit_error("Invalid Use of Page","This page must be called with a group or organization id.");
}

site_header(array(title=>"Image File List"));
$res_image = db_query($query);

while ($row_image = db_fetch_array($res_image)) {
	print '<P>'.html_imagevar($row_image[image_id])."\n<HR>";
}

site_footer(array());
site_cleanup(array());
?>
