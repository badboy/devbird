<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");
if(!defined('IN_USER_FORMULAR')) die("Direct access not allowed");

$id = htmlspecialchars($_GET['id']);
$action = htmlspecialchars($_GET['action']);

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

if(!$hide_form) {
?>
<form action="<?=$Blog->adminrootpath; ?>/user_formular.db/<?=$id ?>/<?=$action ?>" method="post">
 <input type="hidden" name="user_id" value="<?=$id ?>" />
 <fieldset>
  <legend>Name</legend>
    <p><input type="text" class="bigger_input" name="user_name" value="<?= $user_name ?>" /></p>
 </fieldset>

 <fieldset>
  <legend>eMail</legend>
    <p><input type="text" class="bigger_input" name="user_mail" value="<?= $user_mail ?>" /></p>
 </fieldset>

<? if(!$hide_rights) {?>
 <fieldset>
  <legend>Rechte</legend>
    <p><input type="checkbox" name="user_right_sett" value="on" <?=$right_sett ? 'checked="checked" ' : '' ?>/> Einstellungen bearbeiten</p>
    <p><input type="checkbox" name="user_right_art" value="on" <?=$right_art ? 'checked="checked" ' : '' ?>/> Beiträge verfassen</p>
    <p><input type="checkbox" name="user_right_admin" value="on" <?=$right_admin ? 'checked="checked" ' : '' ?>/> Adminmenü betreten</p>
    <p><input type="checkbox" name="user_right_comm" value="on" <?=$right_comm ? 'checked="checked" ' : '' ?>/> Kommentare verfassen</p>
 </fieldset>
<? } else {?>
 <input type="hidden" name="user_right_disabled" value="true" />
<? } ?>

 <fieldset>
  <legend>Cookies</legend>
    <p><input type="checkbox" name="user_cookies" value="on" <?=$use_cookies ? 'checked="checked" ' : '' ?>/> Cookies für den Login verwenden</p>
 </fieldset>

 <fieldset>
  <legend>Passwort</legend>
    <p><input type="password" class="bigger_input" name="user_pw" /></p>
    <p><input type="password" class="bigger_input" name="user_pw_again" /></p>
    <p>mindestens 5 Zeichen</p>
 </fieldset>

 <fieldset>
  <legend>Optionen</legend>
  <p>
   <input type="submit" value="Speichern" name="user_save" />
   <input type="reset" value="Zurücksetzen" />
  </p>
 </fieldset>
</form>
<? } ?>
