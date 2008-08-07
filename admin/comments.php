<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$allowed = $Blog->user->has_right('edit_articles');

if($allowed)
	include("comment_actions.php");

$comments_num = $Blog->comments_num();
$comments_closed = $Blog->comments_num("WHERE public = '0'");
$new_comment = $Blog->comments_num("WHERE `read` = 0");

$max_p_site = 20;
$cur_page = isset($_GET['p']) ? $_GET['p'] : 0;
$seitenanz = ceil($comments_num/$max_p_site);

?>
<p>Besucher der Seite haben insgesamt <strong><?=$comments_num; ?></strong> <a href="<?=$Blog->adminrootpath; ?>/comments.db">Kommentar<?=($comments_num == 1 ? '' : 'e') ?></a>  verfasst, davon sind <strong><?=$comments_closed ?></strong> gesperrt.</p>
<p>
<?
if($new_comment > 0)
{
 if($new_comment == 1)
  echo "<img src=\"{$Blog->adminrootpath}/images/24-message-warn.png\" alt=\"Message\" /><strong><span style=\"color:red;\">Info:</span> ein neuer <a href=\"{$Blog->adminrootpath}/comments.db\">Kommentar</a></strong>";
 else
  echo "<img src=\"{$Blog->adminrootpath}/images/24-message-warn.png\" alt=\"Message\" /><strong><span style=\"color:red;\">Info:</span> {$new_comment} neue <a href=\"{$Blog->adminrootpath}/comments.db\">Kommentare</a></strong>";
}
else
{
 echo '<img src="'.$Blog->adminrootpath.'/images/24-em-check.png" alt="Message" />keine neuen Kommentare';
}
?>
</p>
<br />
<?
if(!empty($error_msg))
{
  echo "<p class=\"message error\">{$error_msg}</p><br />\n";
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

<table>
<thead>
 <th>ID</th>
 <th>Zu News</th>
 <th>Name</th>
 <th>Erstellt</th>
 <th>Status</th>
 <th>Optionen</th>
</thead>
<tbody>
<?
$res = $Blog->get_comments( (isset($_GET['p']) ? $_GET['p'] : 0) , 20, true);
if(!$res)
{
 echo <<<EOF
<tr>
 <td colspan="6" align="center" style="color:red;"><strong>Leider ist ein Fehler aufgetreten</strong></td>
</tr>
<tr>
 <td colspan="6" align="center" style="color:red;">{$Blog->error()}</td>
</tr>
EOF;
}
else {
while($comment = $res->fetch_object())
{
 $date = date("d.m.Y - H:i",$comment->date);
 if($comment->public > 0)
 {
   $status = "veröffentlicht";
   if($allowed)
     $status .= " [<a class=\"close\" href=\"{$Blog->adminrootpath}/comments.db/{$comment->id}/close\">sperren</a>]";
 }
 else
 {
   $status = "<span style=\"color:red\">nicht veröffentlicht</span>";
   if($allowed)
     $status .= " [<a class=\"open\" href=\"{$Blog->adminrootpath}/comments.db/{$comment->id}/open\">entsperren</a>]";
 }

 if($comment->read == 0)
 {
   $comment->name .= ' <img src="'.$Blog->adminrootpath.'/images/24-message-warn.png" alt="Message" /> ';
 }

 $options = "[<a href=\"{$Blog->adminrootpath}/comment_formular.db/{$comment->id}/show\">lesen</a>]";
 if($allowed)
  $options .= " [<a href=\"{$Blog->adminrootpath}/comments.db/{$comment->id}/delete\" onclick=\"return confirm('Möchtest du diesen Kommentar wirklich löschen?');\">löschen</a>]";

 $name = stripslashes($comment->name);
 $news_title = stripslashes($comment->news_title);

 echo <<<EOF
<tr>
 <td>{$comment->id}</td>
 <td><a href="{$Blog->adminrootpath}/formular.db/{$comment->news_id}/edit">{$news_title}</a></td>
 <td><a href="{$Blog->adminrootpath}/comment_formular.db/{$comment->id}/show">{$name}</a></td>
 <td>{$date}</td>
 <td>{$status}</td>
 <td>{$options}</td>
</tr>

EOF;
}
} // if-else end
?>
</tbody>
</table>
