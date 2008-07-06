<?
echo "<h2>Die Tagsuche nach '".htmlspecialchars($_GET['tag'])."' ergab folgende Treffer:</h2><br /><br />\n";
$Blog->fetch_news_by_tag($_GET['tag'], $_GET['p']);
$tag = htmlspecialchars($_GET['tag']);
$pages = $Blog->get_np_page();

if($pages['next'] !== false) {
?>
<div><a class="next" href="<?=$Blog->rootpath ?>/tag/<?=$tag ?>/page/<?=$pages['next'] ?>">Neuere Einträge</a></div>
<?
}

if($pages['previous'] !== false) {
?>
<div style="float:left;"><a class="prev" href="<?=$Blog->rootpath ?>/tag/<?=$tag ?>/page/<?=$pages['previous'] ?>">Vorherige Einträge</a></div>
<?
}
?>
