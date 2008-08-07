<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$allowed = $Blog->user->has_right('edit_articles');

if($allowed)
	include("article_actions.php");

$news_num = $Blog->news_num();
$news_closed = $Blog->news_num("WHERE published = '0'");

$max_p_site = 20;
$cur_page = isset($_GET['p']) ? $_GET['p'] : 0;
$seitenanz = ceil($news_num/$max_p_site);

$res = $Blog->query("SELECT created, title FROM {news} WHERE published > 1 ORDER BY created DESC");
if(!$res) $last_entry_date = '(error: '.$Blog->error().')';
else {
 $fetched = $res->fetch_object();
 $last_entry_date = date('d.m.Y - H:i', $fetched->created);
 $last_entry_title = htmlspecialchars($fetched->title);
 $time_diff = time(0) - $fetched->created;

 $woche = $time_diff/60/60/24/7 % 52;
 $tag = $time_diff/60/60/24 % 7;
 $stunde = $time_diff/60/60 % 24;
 $minute = $time_diff / 60 % 60;
 $sekunde = $time_diff % 60;

 if($sekunde < 30) $last_time = "weniger als 30 Sekunden";
 if($sekunde > 30) $last_time = "weniger als eine Minute";
 if($minute > 0) $last_time = $minute.' Minute'.($minute == 1 ? '' : 'n');
 if($stunde > 0) $last_time = $stunde.' Stunde'.($stunde == 1 ? '' : 'n');
 if($tag > 0) $last_time = $tag.' Tag'.($tag == 1 ? '' : 'e');
 if($woche > 0) $last_time = $woche.' Woche'.($woche == 1 ? '' : 'n');
# $last_time = "{$woche} Woche(n), {$tag} Tag(e), {$stunde} Stunde(n), {$minute} Minute(n), {$sekunde} Sekunde(n)";
}

if(!$allowed)
{
  echo "<p class=\"red_center\"><strong>Du bist nicht berechtigt Artikel zu bearbeiten!</strong></p><br />";
}

?>

<p>Es <?=($news_num == 1 ? 'ist' : 'sind') ?> <strong><?=$news_num; ?></strong> <a href="<?=$Blog->adminrootpath; ?>/article.db">Artikel</a> vorhanden, davon <?=($news_closed == 1 ? 'ist' : 'sind') ?> <strong><?=($news_closed == 1 ? 'einer' : $news_closed ) ?></strong> gesperrt.</p>
<p>Der letzte veröffentlichte Eintrag liegt <strong><?=$last_time ?></strong> zurück (<?=$last_entry_title; ?>, <?=$last_entry_date ?>).</p>
<br />
<p style="text-align: center;"><strong><a href="<?=$Blog->adminrootpath ?>/formular.db/0/new">Neuer Artikel</a></strong></p>
<br />
<?
if(!empty($error_msg))
{
  echo "<p class=\"message error\">{$error_msg}</p>\n";
}
?>
<p class="page_links">
<?
for($i = 0; $i < $seitenanz; $i++) {
 if ($i==$cur_page)
   echo "<span class=\"page_now\">".($i+1)."</span>";
 else
   echo"<a class=\"page\" href=\"{$Blog->adminrootpath}/article.db.{$i}\">".($i+1)."</a>";
}
?>
</p>
<table>
<thead>
 <th>ID</th>
 <th>Titel</th>
 <th>Autor</th>
 <th>Datum</th>
 <th>Status</th>
 <th>Optionen</th>
</thead>
<tbody>
<?
$Blog->get_articles( (isset($_GET['p']) ? $_GET['p'] : 0) );
while($art = $Blog->fetch_articles())
{
 $created = date("d.m.Y - H:i",$art->created);
 $published = $art->published > 0 ? date("d.m.Y - H:i",$art->published) : " -----";

 $title = htmlspecialchars(stripslashes($art->title));
 $title_row = "<a href=\"{$Blog->adminrootpath}/formular.db/{$art->id}/edit\">{$title}</a>";
 if(!$allowed) $title_row = $art->title;
 $status2 = '';

 if($art->published > 0)
 {
   $status = "veröffentlicht";
   if($allowed) $status2 = " [<a class=\"close\" href=\"{$Blog->adminrootpath}/article.db/{$art->id}/close\">sperren</a>]";
 }
 else
 {
   $status = "<span style=\"color:red\">nicht veröffentlicht</span>";
   if($allowed) $status2 = " [<a class=\"open\" href=\"{$Blog->adminrootpath}/article.db/{$art->id}/open\">entsperren</a>]";
 }

 if($art->ajax_saved)
	$status2 = " [auto-saved]";

 $status .= $status2;
 $status .= ($art->comments ? " <span style=\"color:green\">[Kommentare an]</span>" : " <span style=\"color:red\">[Kommentare aus]</span>");

 if($allowed)
 {
	 $options =<<<EOF_OPTIONS
[<a href="{$Blog->adminrootpath}/formular.db/{$art->id}/edit">bearbeiten</a>] [<a href="{$Blog->adminrootpath}/article.db/{$art->id}/delete" onclick="return confirm('Möchtest du diesen Artikel wirklich löschen?');">löschen</a>]
EOF_OPTIONS;
 }
 else
 {
	$options = '[Keine Berechtigung]';
 }

 echo <<<EOF
<tr>
 <td>{$art->id}</td>
 <td>{$title_row}</td>
 <td>{$art->writer}</td>
 <td><abbr title="Erstellt: {$created}">{$published}</abbr></td>
 <td>{$status}</td>
 <td>{$options}</td>
</tr>

EOF;
}
?>
</tbody>
</table>

