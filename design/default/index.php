<?
// Wenn Einzelnews Session setzen und gegebenenfalls gesendeten Kommentar speichern
if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$Blog->session();
}

require 'header.php';

if(isset($_GET['tag']))
{
 include 'tag.php';
 require 'footer.php';
 exit();
}

if(isset($_GET['site']))
{
 $Blog->fetch_page($_GET['site']);
 require 'footer.php';
 exit();
}

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$commenthead = "<p><a id=\"comments\" style=\"font-size: 0.9em;margin-left: 10px;\"><u>Kommentare:</u></a></p>\n";
	$commenthead .= "<div style=\"margin-left:30px;margin-right:20px;\">\n";
	$nocomment = "<i>keine Kommentare vorhanden</i><br />";
	if($Blog->fetch_single_news($_GET['id'], $commenthead, $nocomment))
	{
		$captcha_question = $Blog->captcha_question;
		$captcha_result = md5($Blog->captcha_result);
		$news_id = $_GET['id'];
		include "newsform.php";
	}
}
else 
{
	$page = isset($_GET['p']) ? $_GET['p']-1 : 0;
	$Blog->fetch_news($page);
	$pages = $Blog->get_np_page();

	if($pages['next'] !== false) {
?>
<div><a class="next" href="<?=$Blog->rootpath ?>/page/<?=$pages['next'] ?>">Neuere Einträge</a></div>
<?
	}

	if($pages['previous'] !== false) {
?>
<div style="float:left;"><a class="prev" href="<?=$Blog->rootpath ?>/page/<?=$pages['previous'] ?>">Vorherige Einträge</a></div>
<?
	}
}

require "footer.php";
?>
