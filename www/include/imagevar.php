<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: imagevar.php,v 1.10 2000/01/13 18:36:35 precision Exp $

function imagevar_addtodb($varimage,$cat,$group,$caption) {

	$imageattr = GetImageSize($varimage);
	$imagedata = fread(fopen($varimage,'r'),filesize($varimage));
	$imagesize = filesize($varimage);

	if ($imageattr[2]==1) $imagetype = 'image/gif';
	if ($imageattr[2]==2) $imagetype = 'image/jpeg';
	if ($imageattr[2]==3) $imagetype = 'image/png';

	$res_img = db_query('INSERT INTO image '
		.'(image_category,image_type,image_data,group_id,image_bytes,image_caption) '
		."VALUES ('$cat','$imagetype','$imagedata','$group','$imagesize','$caption')");

	$return = db_insertid($res_img);
	return $return;
}

?>
