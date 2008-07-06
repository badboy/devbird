<?
if(!defined('IN_DEVBIRD')) die("Direct access not allowed");

include('pages_actions.php');

$pages_num = $Blog->pages_num();
$pages_offline = $Blog->pages_num("published = '0'");

$allowed = $Blog->user->has_right('edit_articles');
$pages = $Blog->get_pages();
?>
<p>
Es <?=($pages_num == 1 ? 'ist' : 'sind') ?> <strong><?=$pages_num ?></strong> Unterseite<?=($pages_num == 1 ? '' : 'n') ?> vorhanden, davon <?=($pages_offline == 1 ? 'ist' : 'sind') ?> <strong><?=$pages_offline ?></strong> offline.
<br />
</p>
<p style="text-align: center;"><strong><a href="<?=$Blog->adminrootpath ?>/pages_formular.db/0/new">Neue Unterseite</a></strong></p>

<table>
<thead>
 <th>Kurzname</th>
 <th>Titel</th>
 <th>Erstellt</th>
 <th>Veröffentlicht</th>
 <th>Optionen</th>
</thead>
<tbody>

<? 
while($page = $pages->fetch_object())
{
	$published = $page->published > 0 ? date('d.m.Y - H:i',$page->published) : 'nicht veröffentlicht';
	$created = date('d.m.Y - H:i',$page->created);
	$title = $page->short_name;
	$long_title = $page->title;

	if($page->published > 0)
	{
		if($allowed)
			$status = " [<a class=\"close\" href=\"{$Blog->adminrootpath}/pages.db/{$title}/close\">sperren</a>]";
	}
	else
	{
		if($allowed)
			$status = " [<a class=\"open\" href=\"{$Blog->adminrootpath}/pages.db/{$title}/open\">entsperren</a>]";
	}

	$options = <<<EOF
[<a href="{$Blog->adminrootpath}/pages_formular.db/{$title}/edit">bearbeiten</a>] [<a href="{$Blog->adminrootpath}/pages.db/{$title}/delete" onclick="return confirm('Möchtest du diese Unterseite wirklich löschen?');">löschen</a>]
EOF;

	echo <<<EOF
<tr>
 <td><a href="{$Blog->rootpath}/site/{$title}">{$title}</a></td>
 <td>{$long_title}</a></td>
 <td>{$created}</td>
 <td>{$published} {$status}</td>
 <td>{$options}</td>
</tr>

EOF;
}
?>
</tbody>
</table>
