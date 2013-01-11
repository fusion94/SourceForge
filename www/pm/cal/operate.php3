<?
	include ("header.php3");
	include ("config.php3");
	$database = mysql_connect($mysql_hostname, $mysql_username, $mysql_password);
	mysql_select_db($mysql_database, $database);

	$lastday = 1;
	while (checkdate($month,$lastday,$year))
	{
		$lastday++;
	}

	switch ($action)
	{
		case "Delete marked":
			if (!$id)
			{
				echo "<p>You can't delete nothing from the table.";
				break;
			}
			echo "We are about to delete id $id from $mysql_tablename<p>";

			$query = mysql_query("SELECT username FROM $mysql_tablename WHERE id = $id");
			$row = mysql_fetch_array($query);
		
			if ( !$REMOTE_USER )
			{
				mysql_query("DELETE FROM $mysql_tablename WHERE id = '$id'");
				echo "Item Deleted";
			}
			else
			{
				if ( strcmp($row[username], $REMOTE_USER) == 0 )
				{
					mysql_query("DELETE FROM $mysql_tablename WHERE id = '$id'");
					echo "Item Deleted";
				}
				else
				{
					echo "You aren't the original user, you can't delete this";
				}
			}
					

			break;

		case "Modify marked":
			if (!$id)
			{
				echo "<p>You can't modify nothing.";
				break;
			}
			echo "We are about to modify id $id from $mysql_tablename";
			$query = mysql_query("SELECT *, RIGHT(stamp, 8) AS thetime, SUBSTRING(stamp FROM 9 FOR 2) AS theday FROM $mysql_tablename WHERE id = '$id'");
			$row = mysql_fetch_array($query);
			$row[description] = ereg_replace("<br>", "", $row[description]);

			echo "<form method=post action=operate.php3>
				<input type=hidden name=id value='$row[id]'>
				<table>
				<tr>
					<td><b>Username</td>";

			if ( !$REMOTE_USER )
			{
				echo "<td><input type=text name=username size=20 value='$row[username]'></td></tr>";
			}
			else
			{
				if ( strcmp($REMOTE_USER, $row[username]) == 0)
				{
					echo "<td>$REMOTE_USER<input type=hidden name=username value='$REMOTE_USER'></td></tr>";
				}
				else
				{
					echo "<td>Since you are not the original user, you can't change this</td></tr>
						 </table></form>";
					break;
				}
			}

			echo "
				<tr>
					<td><b>Day</td>
					<td><select name=day size=1>";

			for ($i=1; $i<$lastday; $i++)
			{
				if ($i == $row[theday])
					echo "<option value=$i selected>$i</option>";
				else
					echo "<option value=$i>$i</option>";
			}
			echo "</select><select size=1 name=month>";
			for ($i=1; $i<13; $i++)
			{
				$nm = date("F", mktime(0,0,0,$i,1,$year));
				if ($i == $month)
					echo "<option value=$i selected>$nm</option>";
				else
					echo "<option value=$i>$nm</option>";
			}
			echo "</select><select size=1 name=year>";
			for ($i=$year-2; $i<$year+5; $i++)
			{
				if ($i == $year)
					echo "<option value=$i selected>$i</option>";
				else
					echo "<option value=$i>$i</option>";
			}

			echo "</select></tr>

				<tr><td><b>Time (hh:mm:ss)</td>
				<td><input type=text name=time value='$row[thetime]'></td></tr>

				<tr><td><b>Subject (255 chars max)</td>
				<td><input type=text name=subject value=\"$row[subject]\"></td></tr>

				<tr><td><b>Description</td>
				<td><textarea wrap=virtual rows=5 cols=50 name=description>$row[description]</textarea></td></tr>
				</table>
				<input type=hidden name=action value=Addsucker>
				<input type=hidden name=modify value=Modify>
				<input type=submit value=\"Submit item\">
				</form>";    
			break;

		case "Add item to calendar":
			echo "Adding item to calendar";
			$query = mysql_query("SELECT max(id) as id FROM $mysql_tablename");
			if ($query)
			{
				$result = mysql_fetch_array($query);
				$result["id"]++;
			}
			else
			{
				$result["id"] = 0;
			}
			echo "<form method=post action=operate.php3>
				<input type=hidden name=id value=$result[id]>
				<table>
				<tr>	<td><b>Username</td>";

			if ( !$REMOTE_USER )
			{
				echo "<td><input type=text name=username size=20></td></tr>";
			}
			else
			{
				echo "<td>$REMOTE_USER<input type=hidden name=username value='$REMOTE_USER'></td></tr>";
			}
			
			echo "	<tr><td><b>Day</td>
					<td><select name=day size=1>";
			for ($i=1; $i<$lastday; $i++)
			{
				if ($i == $day)
					echo "<option value=$i selected>$i</option>";
				else    
					echo "<option value=$i>$i</option>";
			}
			echo "</select><select size=1 name=month>";
			for ($i=1; $i<13; $i++)
			{
				$nm = date("F", mktime(0,0,0,$i,1,$year));
				if ($i == $month)
					echo "<option value=$i selected>$nm</option>";
				else
					echo "<option value=$i>$nm</option>";
			}
			echo "</select><select size=1 name=year>";
			for ($i=$year-2; $i<$year+5; $i++)
			{
				if ($i == $year)
					echo "<option value=$i selected>$i</option>";
				else
					echo "<option value=$i>$i</option>";
			}
			echo "</select></td></tr>
					<tr><td><b>Time (hh:mm:ss)</td>
					<td><input type=text name=time></td></tr>
					<tr><td><b>Subject (255 chars max)</td>
					<td><input type=text name=subject></td></tr>
					<tr><td><b>Description</td>
					<td><textarea wrap=virtual rows=5 cols=50 name=description></textarea></td></tr>
				</table>
				<input type=hidden name=action value=Addsucker>
				<input type=submit value=\"Submit item\">
				</form>";
			break;

		case "Addsucker":

			if ($modify)
			{
				mysql_query("DELETE FROM $mysql_tablename WHERE id = '$id'");
				ereg_replace("<br>", "", $description);
			}
			$description = nl2br($description);
			$description = addslashes($description);
			$subject = addslashes($subject);

			$temp = mysql_query("INSERT INTO $mysql_tablename (username, stamp, subject, description) VALUES ('$username', '$year-$month-$day $time', '$subject', '$description')");
			if ($temp != 0)
				echo "Item added ...";
			else
			{
				echo "Item may not have been added ...";
				echo mysql_error();
			}

			break;
	}

	echo "<p><form method=get action=welcome.php3><input type=submit value='Back to Calendar'>
		<input type=hidden name=month value=$month><input type=hidden name=year value=$year></form>";
?>
