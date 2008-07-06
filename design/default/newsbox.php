<div class="newsbox">
<div class="newshead">
<h2><a href="<?=$url ?>"><?=$title ?></a></h2><? if(!isset($disable_comments) || !$disable_comments) { ?>
, <a href="<?=$url ?>#comments" title="Kommentare lesen" class="newstitle"><?=$com_count ?> Kommentar<?=($com_count==1?'':'e') ?></a><?= (($Blog->user->has_right('edit_articles') && isset($id)) ? " <a class=\"newstitle\" href=\"{$Blog->adminrootpath}/formular.db/{$id}/edit\">Bearbeiten</a>" : '') ?>
<? } ?>
<p><?=$writer ?>, <?=$date ?></p>
</div> <!-- .newshead -->
<div class="newscontent">
<p><?=$content ?></p>
</div> <!-- .newscontent -->
<? if($tags) { ?>
<p class="news-footer">Tags: <?
foreach($tags as $tag) { ?>
<a href="<?=$rootpath; ?>/tag/<?=$tag ?>"><?=$tag ?></a> 
<? } ?>
</p>
<? } ?>
</div> <!-- .newsbox -->
