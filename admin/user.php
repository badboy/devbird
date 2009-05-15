<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']))
{
 if($_GET['id'] == $Blog->user->id)
 {
	 echo "<p class=\"message error\">Du kannst dich nicht selbst löschen. Bitte einen Admin darum, es für dich zu tun.</p>";
 }
 elseif($Blog->user->has_right('edit_settings'))
 {
	if($Blog->delete_user($_GET['id']))
	{
		echo "<p class=\"message\">Benutzer erfolgreich gelöscht!</p><br />\n";
	}
	else
	{
		echo "<p class=\"message error\">Leider ist beim Löschen ein Fehler aufgetreten. Der Benutzer konnte nicht gelöscht werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.</p><br />\n";
	}
 }
 else
 {
	echo "<p class=\"message error\">Du hast keine Berechtigung Benutzer zu löschen!</p><br />\n";
 }
}

?>
<p style="text-align: center;"><strong><a href="<?=$Blog->adminrootpath ?>/user_formular.db/0/new">Neuen Benutzer anlegen</a></strong></p>
<br />
<table>
<thead>
 <th>User-ID</th>
 <th>Name</th>
 <th>eMail</th>
 <th>
  Einstellungen |
  Beiträge |
  Adminmenü |
  Kommentare
 </th>
 <th>Cookies</th>
 <th>Optionen</th>
</thead>
<tbody>
<?
 $user = User::find_by_id($Blog->user->id);
 $cross =  "<img src=\"{$Blog->adminrootpath}/images/16-em-cross.png\" alt=\"n\" />";
 $check = "<img src=\"{$Blog->adminrootpath}/images/16-em-check.png\" alt=\"y\" />";

 $cookies = ($user->use_cookies == 1 ? $check : $cross);

 if($user->has_right('edit_settings'))
	$status = $check;
 else
	$status = $cross;
 $status .= ' | ';
 $status .= ($user->has_right('edit_articles') ? $check : $cross);
 $status .= ' | ';
 $status .= ($user->has_right('visit_admin') ? $check : $cross);
 $status .= ' | ';
 $status .= ($user->has_right('comment_articles') ? $check : $cross);

 $options = "[<a href=\"{$Blog->adminrootpath}/user_formular.db/{$user->id}/edit\">bearbeiten</a>] [<a href=\"{$Blog->adminrootpath}/user.db\" onclick=\"alert('Du kannst dich nicht selbst löschen'); return false;\">löschen</a>]";

if($user)
{
?>
 <tr>
  <td><?=$user->id ?></td>
  <td><?=$user->name ?></td>
  <td><?=$user->mail ?></td>
  <td><?=$status ?></td>
  <td><?=$cookies ?></td>
  <td><?=$options ?></td>
 </tr>
<?
}
 $options = ''; 
 $users = User::all();
 if(count($users)-1 > 0)
 {
?>
 <tr>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
 </tr>
<?
 }
 foreach($users as $user)
 {
	 if($user->id == $Blog->user->id) continue;
	 $cookies = ($user->use_cookies ? $check : $cross);

	 $status = $user->has_right('edit_settings') ? $check : $cross;
	 $status .= ' | ';
	 $status .= $user->has_right('edit_articles') ? $check : $cross;
	 $status .= ' | ';
	 $status .= $user->has_right('visit_admin') ? $check : $cross;
	 $status .= ' | ';
	 $status .= $user->has_right('comment_articles') ? $check : $cross;

	 if($Blog->user->has_right('edit_settings'))
	 {
		 $options = "[<a href=\"{$Blog->adminrootpath}/user_formular.db/{$user->id}/edit\">bearbeiten</a>] [<a href=\"{$Blog->adminrootpath}/user.db/{$user->id}/delete\" onclick=\"return confirm('Möchtest du diesen Benutzer wirklich löschen?');\">löschen</a>]";
	 }
	 else
		 $options = 'Keine Berechtigung';
?>
 <tr>
  <td><?=$user->id ?></td>
  <td><?=$user->name ?></td>
  <td><?=$user->mail ?></td>
  <td><?=$status ?></td>
  <td><?=$cookies ?></td>
  <td><?=$options ?></td>
 </tr>
<? } ?>

</tbody>
</table>
