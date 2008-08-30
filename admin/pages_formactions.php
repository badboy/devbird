<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");
include "bbcode_functions.php";
if(!empty($id))
{
 $_page = $Blog->get_page($id);
 if($Blog->lastresult->num_rows == 0)
 {
  $id = htmlspecialchars($id);
  $error_msg = "Keine Unterseite mit dem Titel {$id} gefunden!<br />\n<a href=\"{$Blog->adminrootpath}/pages_formular.db/0/new\">Neuen Artikel erstellen</a>";
  $hide_form = true;
  return;
 }
 $out_title = htmlspecialchars(stripslashes($_page->short_name));
 $out_longtitle = htmlspecialchars(stripslashes($_page->title));
 $out_date = htmlspecialchars($_page->created);
 $out_public = $_page->published > 0 ? true : false;
 $out_content = htmlspecialchars(stripslashes($_page->bb_code));
}

if(isset($_POST['page_save']))
{
 // daten einlesen
 $page_title = isset($_POST['page_title']) ? $_POST['page_title'] : '';
 $page_longtitle = isset($_POST['page_longtitle']) ? $_POST['page_longtitle'] : '';
 $page_date = isset($_POST['page_date']) ? $_POST['page_date'] : '';
 $page_public = isset($_POST['page_public']) ? $_POST['page_public'] : '';
 $page_content = isset($_POST['page_content']) ? $_POST['page_content'] : '';
 $page_datechange = isset($_POST['page_datechange']) ? $_POST['page_datechange'] : false;
 $page_comments = isset($_POST['page_comments']) ? $_POST['page_comments'] : false;

 // für erneute ausgabe vorbereiten
 $out_title = htmlspecialchars(stripslashes($page_title));
 $out_longtitle = htmlspecialchars(stripslashes($page_longtitle));
 $out_date = htmlspecialchars($page_date);
 $out_public = $page_public ? true : false;
 $out_content = htmlspecialchars(stripslashes($page_content));

 if(empty($page_title) || empty($page_longtitle) || empty($page_date) || empty($page_content))
 {
   $error_msg = "Bitte alle Felder ausfüllen!";
   return;
 }

 $page_bb = $page_content;
 $page_content = replace_ubbcode($page_content, ($Blog->settings['Standardcodesprache']), $Blog->rootpath);

 if($action == 'edit')
	$page_public = $page_datechange ? ($page_public ? time(0) : 0) : $page_date;
 else
	$page_public = $page_public ? $page_date : 0;
 $page_comments = $page_comments ? 1 : 0;

 $page_shorttitle = $Blog->shorttext($page_title);

 if($Blog->insert_page($action=='edit'?'edit':'new', $page_title, $page_longtitle, $page_date, $page_public, $page_content, $page_bb))
 {
   $done_msg = "Unterseite erfolgreich gespeichert!<br />\n";
   if($page_public > 0)
	$done_msg .= "<br />\n<a href=\"{$Blog->adminrootpath}/pages_formular.db/{$page_shorttitle}/edit\">Weiter bearbeiten</a><br />\n<a href=\"{$Blog->rootpath}/site/{$page_shorttitle}\">Zur Unterseite</a>";
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
