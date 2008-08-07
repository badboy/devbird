<?
require "core.php";

function errorExit($message)
{
	header("Content-type: text/xml");  
	echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<response>
 <error>1</error>
 <message>{$message}</message>
</response>
EOF;
	exit;
}

if(!isset($_GET['id']))
	errorExit('I need the article ID');
if(!isset($_POST['url']))
	errorExit('You must give a perma link to your article');
if(!isset($_POST['excerpt']))
	errorExit('You must give a excerpt of your article');
if(!isset($_POST['blog_name']))
	errorExit('You must give the name of your blog');

$id = $_GET['id'];
$url = htmlspecialchars($_POST['url']);
$excerpt = $_POST['excerpt'];
$blog_name = htmlspecialchars($_POST['blog_name']);

$comment = '';
if(strlen($excerpt) > 255)
{
	$comment = '(...) ' . substr($excerpt, 0, 243) . ' (...)';
}
else
	$comment = $excerpt;


$p = $Blog->getnewsbyid($id);
if(!$p) errorExit('Article not found');
$p = $p->fetch_object();
if(!$p) errorExit('Article not found');

$mytitle = $p->title;

$myurl = $Blog->rootpath.'/'.$p->id.'/'.$Blog->shorttext($mytitle);
##errorExit($myurl);
#if(strpos($comment, $myurl) !== false) errorExit('Go away, spammer');
	
$comment = strip_tags($comment);

$comment = nl2br($comment);
if(isset($_POST['title']))
{
	$title = htmlspecialchars($_POST['title']);
	$comment = "<strong>{$title}</strong><br />" . $comment;
}

if($Blog->save_comment($id, $blog_name, '', $url, $comment, false))
{
	header("Content-type: text/xml");  
	echo <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<response>
 <error>0</error>
</response>
EOF;
}
else
{
	errorExit('Trackback cannot be saved');
}
?>
