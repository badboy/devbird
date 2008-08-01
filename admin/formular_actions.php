<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");
include "bbcode_functions.php";

if($id != 0)
{
 $article = $Blog->get_article($id);
 if($Blog->lastresult->num_rows == 0)
 {
  $id = htmlspecialchars($id);
  $error_msg = "Kein Artikel mit der ID {$id} gefunden!<br />\n<a href=\"{$Blog->adminrootpath}/formular.db/0/new\">Neuen Artikel erstellen</a>";
  $hide_form = true;
  return;
 }
 $out_id = htmlspecialchars($article->id);
 $out_title = htmlspecialchars(stripslashes($article->title));
 $out_writer = htmlspecialchars($article->writer);
 $out_date = htmlspecialchars($article->created);
 $out_public = $article->published > 0 ? true : false;
 $out_content = htmlspecialchars(stripslashes($article->bb_code));
 $out_comments = $article->comments ? 1 : 0;
 $out_tags =  htmlspecialchars($article->tags);
}

if(isset($_POST['article_save']))
{
 // daten einlesen
 $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
 $article_title = isset($_POST['article_title']) ? $_POST['article_title'] : '';
 $article_writer = isset($_POST['article_writer']) ? $_POST['article_writer'] : '';
 $article_date = isset($_POST['article_date']) ? $_POST['article_date'] : '';
 $article_public = isset($_POST['article_public']) ? $_POST['article_public'] : '';
 $article_content = isset($_POST['article_content']) ? $_POST['article_content'] : '';
 $article_tags = isset($_POST['article_tags']) ? $_POST['article_tags'] : '';
 $article_datechange = isset($_POST['article_datechange']) ? $_POST['article_datechange'] : false;
 $article_comments = isset($_POST['article_comments']) ? $_POST['article_comments'] : false;
 
 $article_trackback = isset($_POST['article_trackback']) ? $_POST['article_trackback'] : false;

 // für erneute ausgabe vorbereiten
 $out_id = htmlspecialchars($article_id);
 $out_title = htmlspecialchars(stripslashes($article_title));
 $out_writer = htmlspecialchars($article_writer);
 $out_date = htmlspecialchars($article_date);
 $out_public = $article_public ? true : false;
 $out_content = htmlspecialchars(stripslashes($article_content));
 $out_tags =  htmlspecialchars($article_tags);

 if(empty($article_title) || empty($article_writer) || empty($article_date) || empty($article_content))
 {
   $error_msg = "Bitte alle Felder ausfüllen!";
   return;
 }

 if(empty($article_tags))
 {
  $article_tags = 'main';
 }
 $article_bb = $article_content;
 $article_content = replace_ubbcode($article_content, ($Blog->settings['Extra-BB-Codes'] == 'an' ? true : false), ($Blog->settings['Standardcodesprache']), $Blog->rootpath);

 if($action == 'edit')
	$article_public = $article_datechange ? ($article_public ? time(0) : 0) : $article_date;
 else
	$article_public = $article_public ? $article_date : 0;
 $article_comments = $article_comments ? 1 : 0;

 $article_shorttitle = $Blog->shorttext($article_title);

 if($Blog->insert_article($article_id, $article_title, $article_writer, $article_date, $article_public, $article_content, $article_bb, $article_tags, $article_comments))
 {
	$done_msg = "Artikel erfolgreich gespeichert!<br />\n";
   
   	if(!empty($article_trackback))
	{
		$skip_tb = false;
		if($article_id == 0)
		{
				$article_date = $Blog->DB->real_escape_string($article_date);
				$article_title = $Blog->DB->real_escape_string($article_title);
				$res = $Blog->query("SELECT id FROM {news} WHERE created = '{$article_date}' AND title = '{$article_title}' LIMIT 1");
				if(!$res)
				{
					die($Blog->error());
					$done_msg .= "<span style=\"color:red\">Fehler beim Senden des Trackbacks! (ID kann nicht ausgelesen werden)</span><br />\n";
					$skip_tb = true;
				}
				else
				{
					$tmp = $res->fetch_object();
					$article_id = $tmp->id;	
				}
		}
		if(!$skip_tb)
		{
			$tb_ret = $Blog->send_trackback($article_trackback, $article_id, $article_title, $article_content);
			if($tb_ret['error'] == 0)
			{
				$done_msg .= "Trackback wurde gesendet!<br />\n";
			}
			else
			{
				$done_msg .= "<span style=\"color:red\">Fehler beim Senden des Trackbacks!<br />{$tb_ret['message']}</span><br />\n";
			}
		}
	}
   
	if($article_public > 0 && $article_id > 0)
	$done_msg .= "<br />\n<a href=\"{$Blog->adminrootpath}/formular.db/{$article_id}/edit\">Weiter bearbeiten</a><br />\n<a href=\"{$Blog->rootpath}/{$article_id}/{$article_shorttitle}\">Zum Artikel</a>";
   else
	$done_msg .= "<br />\n<a href=\"{$Blog->rootpath}/\">Zur Seite</a>";
   return;
 }
 else
 {
   $error_msg = "Fehler beim Speichern!<br />\n{$Blog->error()}";
   return;
 }
}

?>
