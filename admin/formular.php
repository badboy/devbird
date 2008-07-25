<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

if(!isset($_GET['id']) || !isset($_GET['action']))
{
  $error_msg = "<a href=\"{$Blog->adminrootpath}/formular.db/0/new\">Neuen Artikel erstellen</a>";
  $hide_form = true;
}


$action = isset($_GET['action']) ? $_GET['action'] : 'new';
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if($action != 'new' && $action != 'edit')
{
  $error_msg = "Keine Aktion gewählt.<br /><a href=\"{$Blog->adminrootpath}/formular.db/0/new\">Neuen Artikel erstellen</a>";
  $hide_form = true;
}

if(!$Blog->user->has_right('edit_articles'))
{
 echo "<p class=\"red_center\"><strong>Tut mir leid! Du bist nicht berechtigt Artikel zu schreiben!</strong></p>";
 return;
}

if(isset($out_id) && !empty($out_id)) $id = $out_id;
else $id = htmlspecialchars($id);
$out_writer = $Blog->user->name;
include("formular_actions.php");

if(isset($out_date) && !empty($out_date)) $timestamp = $out_date;
else $timestamp = time(0);
$date_pub = date('d.m.Y - H:i', $timestamp);

if(!empty($error_msg))
{
?>
 <fieldset>
  <legend>Fehler</legend>
   <p style="color: red;text-align: center;">
    <strong><?=$error_msg ?></strong>
   </p>
 </fieldset>
 <br />
<?
}
if(!empty($done_msg))
{
?>
 <fieldset>
  <legend>Meldung</legend>
   <p style="color: green;text-align: center;">
    <strong><?=$done_msg ?></strong>
   </p>
 </fieldset>
 <br />
<?
 $hide_form = true;
}

$out_title = isset($out_title) ? $out_title : '';
$out_writer = isset($out_writer) ? $out_writer : '';
$out_public = isset($out_public) ? $out_public : 0;
$out_content = isset($out_content) ? $out_content : '';
$out_tags = isset($out_tags) ? $out_tags : 'main ';
$out_comments = isset($out_comments) ? $out_comments : 1;
$out_datechange = isset($out_datechange) ? $out_datechange : 0;

if($action == 'edit')
	$datechange = true;
else
	$datechange = false;

if(!isset($hide_form) || !$hide_form) {
?>
<form method="post" action="<?=$_SERVER['REQUEST_URI'] ?>" class="editor" onsubmit="editorClose();" id="article_editor">
 <input type="hidden" name="article_id" value="<?=$id; ?>" id="article_id" />

 <fieldset>
  <legend>Titel / Verfasser</legend>
   <p>
    <input type="text" class="bigger_input" name="article_title" id="article_title" value="<?=$out_title ?>" /> /
    <input type="text" class="bigger_input" name="article_writer" id="article_writer" value="<?=$out_writer ?>" />
   </p>
 </fieldset>
 
 <fieldset>
  <legend>Datum</legend>
   <p>
    <input type="text" value="<?=$date_pub ?>" readonly="readonly" />
    <input type="hidden" name="article_date" id="article_date" value="<?=$timestamp ?>" />
<? if($datechange) { ?>
    <abbr title="Soll das Datum beim Speichern auf das Jetzige gesetzt werden?">Neues Datum</abbr>? <input type="checkbox" name="article_datechange" <?=($out_datechange ? 'checked="checked" ' : '') ?>/> |
<? } ?>
    <abbr title="Soll der Artikel sofort nach Speichern im Blog erscheinen?">Direkt veröffentlichen</abbr>? <input type="checkbox" name="article_public" <?=($out_public ? 'checked="checked" ' : '') ?>/> |
    <abbr title="Soll es den Besuchern möglich sein den Artikel zu kommentieren?">Kommentare erlauben</abbr>? <input type="checkbox" name="article_comments" <?=($out_comments ? 'checked="checked" ' : '') ?>/>
   </p>
 </fieldset>

 <fieldset>
  <legend>Artikel</legend>
   <p style="float:right;">
    <input class="bigger_input" type="text" value="<?=($Blog->settings['AJAX-Autosave'] == 'an' ? '[not saved]' : '[AJAX-Autosave abgeschaltet]') ?>" readonly="readonly" id="status" />
   </p>
   <p>
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/bold.gif" alt="Bold" title="Bold [b][/b]" onclick="editorAdd('b');" >
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/underline.gif" alt="Underline" title="Underline [u][/u]" onclick="editorAdd('u');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/italic.gif" alt="Italic" title="Italic [i][/i]" onclick="editorAdd('i');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/strike.gif" alt="Strike" title="Strike [s][/s]" onclick="editorAdd('s');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/url.gif" alt="URL" title="URL [url=link][/url]" onclick="editorAdd('url');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/code.gif" alt="Code" title="Code [code=lang][/code]" onclick="editorAdd('code');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/img.gif" alt="Image" title="Image [img][/img&lt;,x,y&gt;]" onclick="editorAdd('img')">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/flash.gif" alt="Flash" title="Flash [flash][/flash]" onclick="editorAdd('flash');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/left.gif" alt="Left" title="Left [left][/left] (erzeugt Umbruch)" onclick="editorAdd('left');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/center.gif" alt="Center" title="Center [center][/center] (erzeugt Umbruch)" onclick="editorAdd('center');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/right.gif" alt="Right" title="Right [right][/right] (erzeugt Umbruch)" onclick="editorAdd('right');">
   </p>
   <p>
    <img src="<?=$Blog->rootpath ?>/smilies/icon_evil.gif" onclick="editorAdd(']:-> ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_idea.gif" onclick="editorAdd(':idea: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_smile.gif" onclick="editorAdd(':) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_wink.gif" onclick="editorAdd(';) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_rolleyes.gif" onclick="editorAdd(':roll: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_confused.gif" onclick="editorAdd(':/ ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_lol.gif" onclick="editorAdd(':lol: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_surprised.gif" onclick="editorAdd(':O ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_sad.gif" onclick="editorAdd(':( ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_cool.gif" onclick="editorAdd('8) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_razz.gif" onclick="editorAdd(':> ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_eek.gif" onclick="editorAdd(':shock: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_cry.gif" onclick="editorAdd(':'( ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_mad.gif" onclick="editorAdd(':mad: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_biggrin.gif" onclick="editorAdd(':D ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_mrgreen.gif" onclick="editorAdd(':green: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_neutral.gif" onclick="editorAdd(':| ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_exclaim.gif" onclick="editorAdd(':!: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_question.gif" onclick="editorAdd(':?: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_arrow.gif" onclick="editorAdd(':>: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_redface.gif" onclick="editorAdd(':red: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_twisted.gif" onclick="editorAdd(':evil: ');">
   </p>

   <p><textarea name="article_content" id="article_content" class="article_text"><?=$out_content ?></textarea></p>
 </fieldset> 
 
 <fieldset>
  <legend>Tags</legend>
  <p>
   <input type="text" name="article_tags" class="bigger_input" value="<?=$out_tags ?>" id="a_tags" />
   <input type="button" value="Tags entfernen"  onclick="$('a_tags').value='';"/>
  </p>
  <p class="subinfo">Mehrere Tags mit einem Leerzeichen trennen</p>
  <p>Vorhandene Tags:<br />
<?
 $tags = $Blog->get_tags();
 if(empty($tags))
	echo "keine Tags gefunden";
 else
 {
	foreach($Blog->get_tags() as $_tag)
	{
		echo "<a href=\"javascript:addTag('{$_tag}');\" title=\"Tag einfügen: {$_tag}\">{$_tag}</a> ";
	}
 }
?>
  </p>
 </fieldset>

 <fieldset>
  <legend>Optionen</legend>
  <p>
   <input type="submit" value="Senden" name="article_save" />
   <input type="reset" value="Zurücksetzen" />
  </p>
 </fieldset>

</form>

<? } ?>
