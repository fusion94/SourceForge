<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: mainmenu.php,v 1.2 2000/01/13 18:36:34 precision Exp $

      ////////////////////////////////////
      //                                //  
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                //
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     //
      //                                //
      ////////////////////////////////////

// CreateGIF function
//   Creates a GIF image defined by the script
//
function CreateGIF($text, $file) {

	// constants
	//
	$script_path = $GLOBALS["gstrPathScripts"]; // set in common.inc
	$bkg_image = "mainmenu.gif";
	$font   = "aqrswfte.pfb";
	$size   = 14;

        // define an image handle
        //
        $im = ImageCreateFromGif($script_path . $bkg_image);

	// set colors
	//
        $black = ImageColorAllocate($im, 0,0,0);
        $white = ImageColorAllocate($im, 255,255,255);
	$textcolor = ImageColorAllocate($im, 72,101,145);

        // write text
        //
	$font_handle = ImagePSLoadFont($script_path . $font);
	$bounding_box = ImagePSBBox($text, $font_handle, $size);
	$x = ImageSX($im) - $bounding_box[2] - 8;
	$y = 12; // magic number; seems to work
	ImagePSText($im, $text, $font_handle, $size, $textcolor, $white, $x, $y, 0, 0, 0, 16);

        // write gif to file
        //
        ImageGif($im, $file);
        ImageDestroy($im);
	ImagePSFreeFont($font_handle);

}

?>
