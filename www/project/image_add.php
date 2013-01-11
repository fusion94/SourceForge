<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: image_add.php,v 1.29 2000/01/20 10:58:13 dtype Exp $

require 'pre.php';    
require 'imagevar.php';
session_require(array(group=>$group_id,admin_flags=>'A'));

if ($GLOBALS[Submit] && $GLOBALS[form_imagedata]) {
	$imageattr = GetImageSize($form_imagedata);
	if (!$imageattr[2]) {
		exit_error("Invalid Image","The image received is not a valid PNG, GIF, or JPG.");
	}

	// add image to database
	$imageid = imagevar_addtodb($form_imagedata,0,$group_id,$form_caption);
	//if (!$imageid) exit_error("Database Insertion Failed","For an unknown reason, the image insertion"
	//	." into the database failed.");


	site_header(array(title=>"Image Addition Successful"));

	print '<P><B>Image Addition Successful</B>
<P>The following image was added to the database:
<P>Width: <B>'.$imageattr[0].'</B>
<BR>Height: <B>'.$imageattr[1].'</B>
<BR>Image Type: <B>';
	if ($imageattr[2]==1) print 'GIF';
	if ($imageattr[2]==2) print 'JPG';
	if ($imageattr[2]==3) print 'PNG';
print '</B>
';
	site_footer(array());
	site_cleanup(array());
	exit;
}

site_header(array(title=>"Add Project Image"));

print '<P>Adding image for project: <B>'.group_getname($group_id).'</B>
<FORM method="post" action="image_add.php" enctype="multipart/form-data">
<P>Image Caption (optional):
<BR><INPUT type="text" size="40" name="form_caption">
<INPUT type="hidden" name="MAX_FILE_SIZE" value="250000000">
<INPUT type="hidden" name="group_id" value="'.$group_id.'">
<BR>File to upload:
<BR><INPUT type="file" name="form_imagedata" size="40">
<P><INPUT type="submit" name="Submit" value="Submit">
</FORM>';

site_footer(array());
site_cleanup(array());
?>
