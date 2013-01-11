<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: submenu.php,v 1.2 2000/01/13 18:36:34 precision Exp $

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
	$bkg_image = "submenu.gif";
	$font   = "aqrswfte.pfb";
	$size   = 12;

        // define an image handle
        //
        $im = ImageCreateFromGif($script_path . $bkg_image);

	// set colors
	//
        $black = ImageColorAllocate($im, 0,0,0);
        $white = ImageColorAllocate($im, 255,255,255);
	$blue = ImageColorAllocate($im, 72,101,145);

	// set the transparency color to the background color
	//
	ImageColorTransparent($im, ImageColorAt($im, 1, 1));

        // write text
        //
	$font_handle = ImagePSLoadFont($script_path . $font);
	$bounding_box = ImagePSBBox($text, $font_handle, $size);
	$x = ImageSX($im) - $bounding_box[2] - 3;
	$y = 10; // magic number; seems to work
	ImagePSText($im, $text, $font_handle, $size, $white, $blue, $x, $y, 0, 0, 0, 16);

        // write gif to file
        //
        ImageGif($im, $file);
        ImageDestroy($im);
	ImagePSFreeFont($font_handle);

}

?>
