<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

$form_closed = isset($form_closed) ? $form_closed : false;
$new_name = isset($new_name) ? stripslashes($new_name) : '';
$new_desc = isset($new_desc) ? stripslashes($new_desc) : '';
$new_link = isset($new_link) ? stripslashes($new_link) : '';

if(isset($_POST['new_link_save']))
{
 $new_name = htmlspecialchars($_POST['name']);
 $new_desc = htmlspecialchars($_POST['desc']);
 $new_link = htmlspecialchars($_POST['link']);
 $type = htmlspecialchars($_POST['type']);
 if(empty($new_name) || empty($new_desc) || empty($new_link))
 {
	echo "<p class=\"message error\">Bitte alle Felder ausfüllen!</p><br />\n";
 }
 else if($Blog->insert_link($new_name, $new_desc, $new_link, $type))
 {
	echo "<p class=\"message\">Link erfolgreich gespeichert!</p><br />\n";
	$form_closed = true;
 }
 else
 {
	echo "<p class=\"message error\">Leider ist beim Speichern ein Fehler aufgetreten. Der Link konnte nicht gespeichert werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.</p><br />\n";
 }
}

if(isset($_POST['link_save']))
{
 $id = $_POST['id'];
 if(isset($_POST['sort']))
	$sort = $_POST['sort'];
 else $sort = '0';
 $name = $_POST['name'];
 $desc = $_POST['desc'];
 $link = $_POST['link'];
 if($Blog->update_link($id, $name, $desc, $link, $sort))
 {
	echo "<p class=\"message\">Link erfolgreich gespeichert!</p><br />\n";
 }
 else
 {
	echo "<p class=\"message error\">Leider ist beim Speichern ein Fehler aufgetreten. Der Link konnte nicht gespeichert werden:<br />\n{$Blog->error()}<br />\nBitte versuche es erneut.</p><br />\n";
 }
}

if($_GET['action'] == 'edit' && isset($_GET['id']))
{
 $id = htmlspecialchars($_GET['id']);
 $link = $Blog->get_link($id);
 $link_name = stripslashes($link->name);
 $link_desc = stripslashes($link->desc);
 $link_link = stripslashes($link->link);
?>
<form action="<?=$Blog->adminrootpath; ?>/link_formular.db/<?=$id ?>/edit" method="post">
<p>
<?
 if($link->category == 0)
 {
?>
 <select style="width: 80px;" name="sort">
<?
$res = $Blog->get_links(0);
$max_links = $res->num_rows;
for($i=1;$i<=$max_links;$i++)
{
	echo "<option value=\"{$i}\"";
	if($i == $link->sort) echo ' selected="selected"';
	echo ">{$i}</option>\n";
}
?>
 </select>
<?
 }
?>
 <input type="hidden" name="id" value="<?=$link->id ?>" />
 <input type="text" class="bigger_input" name="name" value="<?= $link_name ?>" />
 <input type="text" class="bigger_input" name="desc" value="<?= $link_desc ?>" />
 <input type="text" class="bigger_input" name="link" value="<?= $link_link ?>" />
 <br />
 <input type="submit" name="link_save" value="Speichern"> <input type="reset" value="Zurücksetzen">
</p>
</form>
<?
}
else if($_GET['action'] == 'new' && $_GET['id'] == 0)
{
 if(!$form_closed) {
?>
<form action="<?=$Blog->adminrootpath; ?>/link_formular.db/0/new" method="post">
 <fieldset>
  <legend>Typ</legend>
   <p>
   <select name="type" class="bigger_input">
    <option value="0">Interne Links</option>
    <option value="1">Blogroll</option>
   </select>
   </p>
 </fieldset>
 <fieldset>
  <legend>Name</legend>
   <p><input type="text" class="bigger_input" name="name" value="<?= $new_name ?>" /></p>
 </fieldset>
 <fieldset>
  <legend>Beschreibung</legend>
   <p><input type="text" class="bigger_input" name="desc" value="<?= $new_desc ?>" /></p>
 </fieldset>
 <fieldset>
  <legend>Link</legend>
   <p><input type="text" class="bigger_input" name="link" value="<?= $new_link ?>" /></p>
 </fieldset>
 <br />
 <input type="submit" name="new_link_save" value="Speichern"> <input type="reset" value="Zurücksetzen">
</p>
</form>
<?
 }
}
else
{
 echo <<<EOF
<p>Oh...scheint, als ob du hier falsch bist.<br />
Schau doch mal <a href="{$Blog->adminrootpath}">hier</a> vorbei.</p>
EOF;
}
?>

<br />
<p><a href="<?=$Blog->adminrootpath ?>/links.db">Zurück</a></p>
