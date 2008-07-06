<?
require 'header.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';

$search = htmlspecialchars($query);
$type = isset($_GET['type']) ? $_GET['type'] : 'word';
?>
<form method="get" action="<?=$Blog->rootpath ?>/search.php">
 <p>
 <input type="text" value="<?=$search ?>" name="q" />
 <input type="radio" name="type" value="word" <?=($type=='fulltext'?'':'checked="checked" ') ?>/> word
 <input type="radio" name="type" value="fulltext" <?=$type=='fulltext'?'checked="checked" ':'' ?>/> fulltext
 <input type="submit" id="searchsubmit" value="Search" /> 
 </p>
</form>
<br /><br />
<?
if(empty($search))
{
	echo "Noch keine Suche gestartet<br />\n";
}
else
{
	echo "<h2>Die Suche nach '{$search}' ergab folgende Treffer:</h2><br /><br />\n";
	if(!$Blog->fetch_news_by_keywords($search, $type))
		echo "Keinen Eintrag gefunden";
}

require 'footer.php';
?>
