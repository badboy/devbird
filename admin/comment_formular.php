<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$hide_form = false;
$msg = '';
$allowed = $Blog->user->has_right('edit_articles');

if(isset($_POST['answer_comment']))
{
 $id = isset($_POST['id']) ? $_POST['id'] : 0;
 $name = isset($_POST['name']) ? $_POST['name'] : '';
 $email = isset($_POST['mail']) ? $_POST['mail'] : '';
 $website = isset($_POST['website']) ? $_POST['website'] : '';
 $msg = htmlspecialchars($_POST['msg']);
 $news_id = isset($_POST['news_id']) ? $_POST['news_id'] : 0;

 if(empty($name) || empty($email) || empty($msg) || $news_id == 0)
 {
	$error_msg = "Bitte alle Felder ausfüllen!";
 }
 else if($Blog->insert_comment($name, $email, $website, $msg, $news_id))
 {
	$saved_message =  "<p class=\"message\">Kommentar erfolgreich gespeichert!</p><br />\n";
	$hide_form = true;
 }
 else
 {
	$error_msg = "Leider ist beim Speichern ein Fehler aufgetreten. Der Link konnte nicht gespeichert werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
 }

}

if(isset($_GET['action']) && ($_GET['action'] == 'close' || $_GET['action'] == 'open') && isset($_GET['id']))
{
 $id = htmlspecialchars($_GET['id']);
 switch($_GET['action'])
 {
	case 'close':
		if(!$Blog->close_comment($id))
		{
			$error_msg = "Leider ist beim Sperren des Kommentars ein Fehler aufgetreten. <br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
	case 'open':
		if(!$Blog->open_comment($id))
		{
			$error_msg = "Leider ist beim Entsperren des Kommentars ein Fehler aufgetreten. <br />\n{$Blog->error()}<br />\nBitte versuche es erneut.";
		}
		break;
 }
 $_GET['action'] = 'show';
}

if(!empty($error_msg))
{
  echo "<p class=\"message error\">{$error_msg}</p><br />\n";
}

if($_GET['action'] == 'show' && isset($_GET['id']))
{
 $id = htmlspecialchars($_GET['id']);
 $comment = $Blog->get_comment($id, true);
 $news = $Blog->get_article($comment->news_id);

 $com_name = stripslashes($comment->name);
 $website = stripslashes($comment->website);
 $email = stripslashes($comment->email);
 $com_msg = stripslashes($comment->msg);
 if(empty($comment->website)) $name =  $comment->name;
 else $name = "<a href=\"{$website}\">{$com_name}</a>";

 if($comment->public > 0)
 {
   $status = "veröffentlicht";
   if($allowed)
     $status .= " [<a class=\"close\" href=\"{$Blog->adminrootpath}/comment_formular.db/{$comment->id}/close\">sperren</a>]";
 }
 else
 {
   $status = "<span style=\"color:red\">nicht veröffentlicht</span>";
   if($allowed)
     $status .= " [<a class=\"open\" href=\"{$Blog->adminrootpath}/comment_formular.db/{$comment->id}/open\">entsperren</a>]";
 }
?>
<div class="comment">
 <div class="comment_head">
  <p>von <?=$name ?> (<a href="mailto:<?=$email ?>"><?=$email ?></a>) am <?= date('d.m.Y - H:i', $comment->date); ?> zu <a href="<?=$Blog->rootpath ?>/<?=$comment->news_id ?>/<?=$news->short_name ?>"><?=stripslashes($news->title) ?></a> (<?=$status ?>)</p>
 </div>
<p>
<?=$com_msg ?>
</p>
</div>

<br />
<? if(!$hide_form) { ?>
<h4>Direkte Antwort:</h3>
<form action="<?=$Blog->adminrootpath; ?>/comment_formular.db/<?=$id ?>/show" method="post" class="comment_form">
 <input type="hidden" value="<?=$comment->news_id ?>" name="news_id" />
 <fieldset>
  <legend>Name</legend>
   <p><input type="text" class="bigger_input" name="name" value="<?=$Blog->user->name ?>" /></p>
 </fieldset>
 <fieldset>
  <legend>eMail</legend>
   <p><input type="text" class="bigger_input" name="mail" value="<?=$Blog->user->email ?>" /></p>
 </fieldset>
 <fieldset>
  <legend>Webseite</legend>
   <p><input type="text" class="bigger_input" name="website" value="<?=$Blog->rootpath ?>" /></p>
 </fieldset>
 <fieldset>
  <legend>Kommentar</legend>
   <p><textarea name="msg" class="bigger_input text"><?=$msg ?></textarea></p>
 </fieldset>
 <br />
 <input type="submit" name="answer_comment" value="Speichern"> <input type="reset" value="Zurücksetzen">
</p>
</form>
<?
}
else
{
 echo $saved_message;
} ?>
<br />
<p><a href="<?=$Blog->adminrootpath ?>/comments.db">Zurück</a></p>
<? } ?>
