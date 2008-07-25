<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$allowed = $Blog->user->has_right('edit_settings');

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']))
{
 if($Blog->delete_link($_GET['id']))
 {
	echo "<p class=\"message\">Link erfolgreich gelöscht!</p><br />\n";
 }
 else
 {
	echo "<p class=\"message error\">Leider ist beim Löschen ein Fehler aufgetreten. Der Link konnte nicht gelöscht werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.</p><br />\n";
 }
}

$res = $Blog->get_links(2);
$link_total = $res->num_rows;
$res = $Blog->get_links();
$link_extern = $res->num_rows;
?>
<p>Es <?=($link_total == 1 ? 'ist' : 'sind')?> <strong><?=$link_total ?></strong> Links vorhanden, davon <?=($link_extern == 1 ? 'ist' : 'sind')?> <strong><?=$link_extern ?></strong> extern.</p>
<?
if($allowed) {
?>
<p style="text-align: center;"><strong><a href="<?=$Blog->adminrootpath ?>/link_formular.db/0/new">Neuen Link anlegen</a></strong></p>
<?
}
?>
<br />
<h2>Seiteninterne Links</h2>
<table>
<thead>
 <th>Reihenfolge</th>
 <th>Name</th>
 <th>Beschreibung</th>
 <th>Link</th>
 <th>Optionen</th>
</thead>
<tbody>
<?
$Blog->get_links(0);
while($link = $Blog->fetch_links())
{
 $options =<<<EOF_OPTIONS
[<a href="{$Blog->adminrootpath}/link_formular.db/{$link->id}/edit">bearbeiten</a>] [<a href="{$Blog->adminrootpath}/links.db/{$link->id}/delete" onclick="return confirm('Möchtest du diesen Link wirklich löschen?');">löschen</a>]
EOF_OPTIONS;
 if(!$allowed) $options = '[Keine Berechtigung]';

 $link_name = stripslashes($link->name);
 $link_desc = stripslashes($link->desc);
 $link_link = stripslashes($link->link);

 echo <<<EOF
<tr>
 <td>{$link->sort}</td>
 <td>{$link_name}</td>
 <td>{$link_desc}</td>
 <td>{$link_link}</td>
 <td>{$options}</td>
</tr>

EOF;
}
?>
</tbody>
</table>
<br />
<h2>Blogroll</h2>
<table>
<thead>
 <th>Name</th>
 <th>Beschreibung</th>
 <th>Link</th>
 <th>Optionen</th>
</thead>
<tbody>
<?
if(!$Blog->get_links(1)) die($Blog->error());
while($link = $Blog->fetch_links())
{
 $options =<<<EOF_OPTIONS
[<a href="{$Blog->adminrootpath}/link_formular.db/{$link->id}/edit">bearbeiten</a>] [<a href="{$Blog->adminrootpath}/links.db/{$link->id}/delete" onclick="return confirm('Möchtest du diesen Artikel wirklich löschen?');">löschen</a>]
EOF_OPTIONS;
 if(!$allowed) $options = '[Keine Berechtigung]';

 $link_name = stripslashes($link->name);
 $link_desc = stripslashes($link->desc);
 $link_link = stripslashes($link->link);

 echo <<<EOF
<tr>
 <td>{$link_name}</td>
 <td>{$link_desc}</td>
 <td>{$link_link}</td>
 <td>{$options}</td>
</tr>

EOF;
}
?>
</tbody>
</table>
