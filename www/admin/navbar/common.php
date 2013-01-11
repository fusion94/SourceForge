<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: common.php,v 1.2 2000/01/13 18:36:34 precision Exp $

      ////////////////////////////////////
      //                                //
      //      VA Linux Menu Server      //
      //       http://valinux.com       //
      //                                // 
      // James Byers - jbyers@linux.com //
      //   C. 1999 VA Linux Systems     // 
      //                                //
      ////////////////////////////////////

// set required paths; note that the images path *must* be writable by the httpd user/group
//
$gstrPathScripts = "/www/vaweb/cartman/navbar/scripts/";
$gstrPathImages  = "/www/vaweb/images/navbar/";
$gstrPathImageURL = "/images/navbar/";

function ErrorPage($strError) {
?>

    <center>
      <img src='http://www.varesearch.com/images/logo.jpeg'><br>
      <h3>ERROR:</h3><br>
      <p><?php print $strError; ?>
    </center>

<?php
}
?
