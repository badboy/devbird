<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

if(!isset($_GET['id']) || !isset($_GET['action']))
{
  $error_msg = "<a href=\"{$Blog->adminrootpath}/pages_formular.db/0/new\">Neue Unterseite erstellen</a>";
  $hide_form = true;
}


$action = isset($_GET['action']) ? $_GET['action'] : 'new';
$id = isset($_GET['id']) ? ($_GET['id'] == '0' ? "" : $_GET['id']) : "";

if($action != 'new' && $action != 'edit')
{
  $error_msg = "Keine Aktion gewählt.<br /><a href=\"{$Blog->adminrootpath}/pages_formular.db/0/new\">Neue Unterseite erstellen</a>";
  $hide_form = true;
}

if(!$Blog->user->has_right('edit_articles'))
{
 echo "<p class=\"red_center\"><strong>Tut mir leid! Du bist nicht berechtigt Unterseiten anzulegen!</strong></p>";
 return;
}

if(isset($out_id) && !empty($out_id)) $id = $out_id;
else $id = htmlspecialchars($id);
include("pages_formactions.php");

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

if(isset($out_date) && !empty($out_date)) $timestamp = $out_date;
else $timestamp = time(0);
$date_pub = date('d.m.Y - H:i', $timestamp);

if($action == 'edit')
	$datechange = true;
else
	$datechange = false;

$out_title = isset($out_title) ? $out_title : '';
$out_longtitle = isset($out_longtitle) ? $out_longtitle : '';
$out_public = isset($out_public) ? $out_public : 0;
$out_content = isset($out_content) ? $out_content : "{{newbox=}}\n";
$out_datechange = isset($out_datechange) ? $out_datechange : 0;

if(!isset($hide_form) || !$hide_form) {
?>
<form method="post" action="<?=$_SERVER['REQUEST_URI'] ?>" class="editor" id="page_editor">
 <input type="hidden" name="page_id" value="<?=$id; ?>" id="page_id" />

 <fieldset>
  <legend>Kurztitel</legend>
   <p>
    <input type="text" class="bigger_input" name="page_title" id="page_title" value="<?=$out_title ?>" />
    <br /> 
    alle Sonderzeichen und Umlaute werden durch '-' ersetzt, der komplette Titel in Kleinbuchstaben umgewandelt
   </p>
 </fieldset>

 <fieldset>
  <legend>Titel</legend>
   <p>
    <input type="text" class="bigger_input" name="page_longtitle" id="page_longtitle" value="<?=$out_longtitle ?>" />
    <br /> 
    Erscheint beispielsweise beim Gebrauch von Devbird::fetch_pagelinks();
   </p>
 </fieldset>
 
 <fieldset>
  <legend>Datum</legend>
   <p>
    <input type="text" value="<?=$date_pub ?>" readonly="readonly" />
    <input type="hidden" name="page_date" id="page_date" value="<?=$timestamp ?>" />
<? if($datechange) { ?>
    <abbr title="Soll das Datum beim Speichern auf das Jetzige gesetzt werden?">Neues Datum</abbr>? <input type="checkbox" name="page_datechange" <?=($out_datechange ? 'checked="checked" ' : '') ?>/> |
<? } ?>
    <abbr title="Soll der Artikel sofort nach Speichern im Blog erscheinen?">Direkt veröffentlichen</abbr>? <input type="checkbox" name="page_public" <?=($out_public ? 'checked="checked" ' : '') ?>/>
   </p>
 </fieldset>

 <fieldset>
  <legend>Artikel</legend>
   <p style="float:right;">
    <a href="javascript:void(0);" onclick="editor_add('newbox');">Neue Newsbox durch {{newbox}} erzeugen</a>
   </p>
   <p>
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/bold.gif" alt="Bold" title="Bold [b][/b]" onclick="editor_add('b');" >
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/underline.gif" alt="Underline" title="Underline [u][/u]" onclick="editor_add('u');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/italic.gif" alt="Italic" title="Italic [i][/i]" onclick="editor_add('i');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/strike.gif" alt="Strike" title="Strike [s][/s]" onclick="editor_add('s');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/url.gif" alt="URL" title="URL [url=link][/url]" onclick="editor_add('url');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/code.gif" alt="Code" title="Code [code=lang][/code]" onclick="editor_add('code');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/img.gif" alt="Image" title="Image [img][/img&lt;,x,y&gt;]" onclick="editor_add('img')">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/flash.gif" alt="Flash" title="Flash [flash][/flash]" onclick="editor_add('flash');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/left.gif" alt="Left" title="Left [left][/left] (erzeugt Umbruch)" onclick="editor_add('left');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/center.gif" alt="Center" title="Center [center][/center] (erzeugt Umbruch)" onclick="editor_add('center');">
    <img class="insbutton" src="<?=$Blog->rootpath ?>/images/right.gif" alt="Right" title="Right [right][/right] (erzeugt Umbruch)" onclick="editor_add('right');">
   </p>
   <p>
    <img src="<?=$Blog->rootpath ?>/smilies/icon_evil.gif" onclick="editor_add(']:-> ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_idea.gif" onclick="editor_add(':idea: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_smile.gif" onclick="editor_add(':) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_wink.gif" onclick="editor_add(';) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_rolleyes.gif" onclick="editor_add(':roll: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_confused.gif" onclick="editor_add(':/ ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_lol.gif" onclick="editor_add(':lol: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_surprised.gif" onclick="editor_add(':O ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_sad.gif" onclick="editor_add(':( ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_cool.gif" onclick="editor_add('8) ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_razz.gif" onclick="editor_add(':> ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_eek.gif" onclick="editor_add(':shock: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_cry.gif" onclick="editor_add(':'( ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_mad.gif" onclick="editor_add(':mad: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_biggrin.gif" onclick="editor_add(':D ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_mrgreen.gif" onclick="editor_add(':green: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_neutral.gif" onclick="editor_add(':| ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_exclaim.gif" onclick="editor_add(':!: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_question.gif" onclick="editor_add(':?: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_arrow.gif" onclick="editor_add(':>: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_redface.gif" onclick="editor_add(':red: ');">
    <img src="<?=$Blog->rootpath ?>/smilies/icon_twisted.gif" onclick="editor_add(':evil: ');">
   </p>

   <p><textarea name="page_content" id="article_content" class="article_text"><?=$out_content ?></textarea></p>
 </fieldset> 
 
 <fieldset>
  <legend>Optionen</legend>
  <p>
   <input type="submit" value="Senden" name="page_save" />
   <input type="reset" value="Zurücksetzen" />
  </p>
 </fieldset>

</form>

<? } ?>
