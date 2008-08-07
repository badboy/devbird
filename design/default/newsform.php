<?
	$blog_name = isset($Blog->done['name']) ? $Blog->done['name'] : '';
	$blog_email = isset($Blog->done['email']) ? $Blog->done['email'] : '';
	$blog_website = isset($Blog->done['website']) ? $Blog->done['website'] : '';
	$blog_comment = isset($Blog->done['comment']) ? $Blog->done['comment'] : '';
?>
<br />
<div style="margin-left:30px;margin-right:20px;">
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>#commentform" method="post">
<div class="newsbox">
 <div class="newshead">
  <h3 id="commentform">Kommentar schreiben</h3>
 </div> <!-- .newshead -->
 <div class="newscontent">
<?
if($Blog->done['msg'] !== false) {
 echo $Blog->done['msg'];
}
#if($showall) {
else {
 if($Blog->done['error'] !== false) echo $Blog->done['error'].'<br />';
?>
<input type="hidden" name="newsid" value="<?=$news_id ?>" />
<input type="hidden" name="human" value="<?=$captcha_result ?>" />

<fieldset>
 <input type="text" name="nickname" size="25" class="contact_input" value="<?= $blog_name; ?>" />
 <label for="nickname">Name <span style="color:red">*</span></label>
</fieldset>

<fieldset>
 <input type="text" name="email" size="25" class="contact_input" value="<?= $blog_email; ?>" />
 <label for="email">eMail <span style="color:red">*</span> <span style="font-size:12px">(wird nicht veröffentlicht)</span></label>
</fieldset>

<fieldset>
 <input type="text" name="webseite" size="25" class="contact_input" value="<?= $blog_website; ?>" />
 <label for="webseite">Website</label>
</fieldset>

<fieldset style="margin-top:5px;margin-bottom:5px;">
<textarea cols="50" rows="8" class="contact_input" name="msg"><?= $blog_comment; ?></textarea>
</fieldset>
<?=$captcha_question; ?>
<fieldset style="margin-top:5px;">
<input type="text" name="solution" size="25" class="contact_input" />
<label for="solution">Captcha <span style="color:red">*</span> <i>(in Ziffern)</i></label>
</fieldset>

<fieldset style="margin-top:5px;">
 <input type="checkbox" name="cookie_save" class="contact_input" value="on" />
 <label for="solution">Daten in Cookie speichern (Name, eMail, Website)</label>
</fieldset>

<p style="margin-top:5px;"><input class="contact_input" type="submit" name="comment_submit" value="Senden"></p>
<p style="border-top:1px solid black;margin-top:10px;">Mit <span style="color:red">*</span> markierte Felder sind Pflicht</p>
<? } ?>
 </div> <!-- .newscontent -->
</div> <!-- .newsbox -->
</form>
</div>
