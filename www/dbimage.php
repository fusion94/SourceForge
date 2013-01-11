<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: dbimage.php,v 1.8 2000/01/13 18:36:34 precision Exp $

require 'database.php';
db_connect();

$res_img = db_query("SELECT image_data,image_type FROM image WHERE image_id=$image_id");
$row_img = db_fetch_array($res_img);

// output image
header("Content-Type: $row_img[image_type]");
echo stripslashes($row_img[image_data]);
?>
