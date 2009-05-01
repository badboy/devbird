<?
require 'core.php';
$possible_sites = array('overview', 'article', 'comments', 'links', 'user', 'settings', 'formular', 'link_formular', 'comment_formular', 'user_formular', 'pages', 'pages_formular', 'login', 'logout');
$title = array(
'overview' => '&Uuml;bersicht',
'article'=>'Artikel', 
'comments' => 'Kommentare',
'links' => 'Links', 
'user' => 'User',
'settings' => 'Einstellungen',
'formular' => 'Artikel eintragen',
'link_formular' => 'Link eintragen',
'comment_formular' => 'Kommentar lesen',
'user_formular' => 'Userdaten bearbeiten',
'pages' => 'Unterseiten',
'pages_formular' => 'Unterseite eintragen',
'login' => 'Einloggen',
'logout' => 'Ausloggen'
);
$cur_site = '';

if(!isset($_GET['site']))
 $cur_site = 'overview';
else
 $cur_site = htmlspecialchars($_GET['site']);

if(!in_array($cur_site, $possible_sites))
	header("Location: {$Blog->rootpath}/error");

$cur_title = $title[$cur_site];

if(!$Blog->user->is_online() || $cur_site == 'login')
{
	$cur_title = 'Login';
	include 'login.php';
	include 'footer.php';
	return;
}

if($cur_site == 'logout')
{
	$cur_title = 'Logout';
	$Blog->user->logout();
	$Blog->user = new User;
	include 'header_user.php';
	echo "<h1>{$cur_title}</h1>\n";
	include 'logout.php';
	include 'footer.php';
	return;
}

if(!$Blog->user->has_right('visit_admin'))
	header("Location: {$Blog->rootpath}");

include 'header.php';
echo "<h1>{$cur_title}</h1>\n";
if($cur_site == 'overview')
{
$news_num = $Blog->news_num();
$news_closed = $Blog->news_num("WHERE published = '0'");
$comments_num = $Blog->comments_num();
$comments_closed = $Blog->comments_num("WHERE public = '0'");
$new_comment = $Blog->comments_num("WHERE `read` = 0");


$res = $Blog->query("SELECT created, title FROM {news} WHERE published > 0 ORDER BY created DESC");
if(!$res) $last_entry_date = '(error: '.$Blog->error().')';
else {
 $fetched = $res->fetch_object();
 $last_entry_date = date('d.m.Y - H:i', $fetched->created);
 $last_entry_title = htmlspecialchars(stripslashes($fetched->title));
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

?>
<?
if(is_dir('../install'))
{
 echo '<p style="color:red;font-weight:bold;">Oh! Du solltest den Installationsordner install l&ouml;schen oder umbennen, sonst installiert noch irgendwer Devbird neu.</p><br />';
}
if(is_dir('../update'))
{
 echo '<p style="color:red;font-weight:bold;">Oh! Da scheint noch ein Update-Ordner zu existieren.<br />Ist hier die neueste Version installiert? Wenn ja, dann kannst du den Update-Ordner getrost löschen, ansonsten <a href="../update">aktualisiere jetzt</a>.</p><br />';
}
if(is_writable('../config/config.php'))
{
 echo "<p style=\"color:red;font-weight:bold;\">Oh! Die Konfigurationsdatei config/config.php ist schreibbar. Es w&auml;re besser, wenn diese nur lesbar w&auml;re.</p><br />";
}
?>
<p>Es <?=($news_num == 1 ? 'ist' : 'sind') ?> <strong><?=$news_num; ?></strong> <a href="<?=$Blog->adminrootpath; ?>/article.db">Artikel</a> vorhanden, davon <?=($news_closed == 1 ? 'ist' : 'sind') ?> <strong><?=($news_closed==1?'einer':$news_closed) ?></strong> gesperrt.</p>
<p>Der letzte veröffentlichte Eintrag liegt <strong><?=$last_time ?></strong> zurück (<?=$last_entry_title; ?>, <?=$last_entry_date ?>).</p>
<br />
<p>Besucher der Seite haben insgesamt <strong><?=$comments_num; ?></strong> <a href="<?=$Blog->adminrootpath; ?>/comments.db">Kommentar<?=($comments_num == 1 ? '' : 'e') ?></a>  verfasst, davon sind <strong><?=$comments_closed ?></strong> gesperrt.</p>
<p>
<?
if($new_comment > 0)
{
 if($new_comment == 1)
  echo "<img src=\"images/24-message-warn.png\" alt=\"Message\" /><strong><span style=\"color:red;\">Info:</span> ein neuer <a href=\"{$Blog->adminrootpath}/comments.db\">Kommentar</a></strong>";
 else
  echo "<img src=\"images/24-message-warn.png\" alt=\"Message\" /><strong><span style=\"color:red;\">Info:</span> {$new_comment} neue <a href=\"{$Blog->adminrootpath}/comments.db\">Kommentare</a></strong>";
}
else
{
 echo '<img src="images/24-em-check.png" alt="Message" />keine neuen Kommentare';
}
?>
</p>
<br />
<p style="text-align: center;"><strong><a href="<?=$Blog->adminrootpath ?>/formular.db/0/new">Neuer Artikel</a></strong></p>

<?
}
else {
 define('IN_DEVBIRD', 'true');
 include "{$cur_site}.php";
}

include 'footer.php';
?>
