<?php
//
// SourceForge: Breaking Down the Barriers to Open Source Development
// Copyright 1999-2000 (c) The SourceForge Crew
// http://sourceforge.net
//
// $Id: details_user.php,v 1.5 2000/01/13 18:36:34 precision Exp $

$sql = "SELECT * FROM unix_user WHERE id = $uid";

$result = db_query($sql);

$array = db_fetch_array($result);

?>
<p>&nbsp;</p>

<table border="0" width="99%">
  <tr>
    <td width="150"><strong>User Addition Request</strong></td>
    <td width="450"></td>
  </tr>
  <tr><?php echo "<form action=\"$PHP_SELF?action=update&uid=$uid\" method=\"post\">\n"; ?>
    <td width="150">UserName:</td><td width="150"><?php echo "<b><font size=+2>&nbsp;&nbsp;$array[username]</font></b>"; ?></td>
  </td>
    
    <td width="150">Web User ID:</td>
    <td width="150"><input type="text" name="user_id" <?php echo "value=\"$array[user_id]\""; ?> size="10"></td>
  </tr>
  <tr>
    <td width="150">Status:</td>
    <td width="150"><select name="status">
    	<option <?php if ($array[status] == 1) { echo "selected"; } ?> value=1>Active</option>
    	<option <?php if ($array[status] == 2) { echo "selected"; } ?> value=2>Deleted</option>
    	<option <?php if ($array[status] == 3) { echo "selected"; } ?> value=3>Suspended</option>
    </select></td>

    <td width="150">Password:</td>
    <td width="150"><input type="password" <?php if($form_password) { echo "value=\"$form_password\""; } ?> name="form_password" size="15"></td>

  </tr>
  <tr>
    <td width="150">Shell:</td>
    <td width="150"><select name="shell">

	<?php
		// Get all the available Shells
      		$shells = file("/etc/shells");
		
		for ($i = 0; $i < count($shells); $i++) {
			$this_shell = chop($shells[$i]);

			if ($this_shell == $array[shell]) {
				echo "<option selected value=$this_shell>$this_shell</option>\n";
			} else {
				echo "<option value=$this_shell>$this_shell</option>\n";
			}
		}
	?>

    </select></td>
    <td width="150">Retype Password:</td>
    <td width="150"><input type="password" name="password_retype" <?php if($password_retype) { echo "value=\"$password_retype\""; } ?>  size="15"></td>
 

  <tr>
    <td width="150"></td>
    <td width="450"><input type="submit" value="Submit" name="submit"></td>



   </td></form>
  </tr>
</table>
