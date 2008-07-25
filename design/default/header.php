<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
  <title><?=$Blog->buildtitle() ?> | <?=$Blog->settings['Blogname'] ?></title>
 <base href="<?=$Blog->design(1) ?>/" />
 <meta http-equiv="Content-Type" content="text/html; charset=<?=$Blog->encoding ?>" />
 <meta name="description" content="<?=$Blog->settings['Blogname'] ?> - <?=$Blog->settings['Blogbeschreibung'] ?>" />
 <meta name="author" content="BadBoy_" />
 <link rel="shortcut icon" href="<?=$Blog->rootpath ?>/favicon.ico" type="image/x-icon" /> 
 <link rel="alternate"  type="application/rss+xml" title="<?=$Blog->settings['Blogbeschreibung'] ?>" href="<?=$Blog->rootpath ?>/feed.php" />

<? $Blog->include_lightbox(); ?>

 <link rel="stylesheet" type="text/css" href="css/basic.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="css/newsbox.css" media="screen" />
</head>
<body>
<div id="main">
 <div id="header">
  <!-- Logografik -->
  <h1><?=$Blog->settings['Blogname'] ?></h1>
 </div>
 <div id="navigation">
<div class="navibox">
<h2>Menü</h2>
<ul>
<?php
 $Blog->fetch_links();
?>
</ul>
</div>
<div class="navibox">
<h2>Seiten</h2>
<ul>
<?php
 $Blog->fetch_pagelinks();
?>
</ul>
</div>
<?
if($Blog->user->is_online())
{
?>
<div class="navibox">
<h2>User: <?=$Blog->user->name ?></h2>
<ul>
<? if($Blog->user->has_right('visit_admin')) { ?>
 <li><a href="<?=$Blog->adminrootpath ?>/">Adminmenü</a></li>
<? } ?>
 <li><a href="<?=$Blog->adminrootpath ?>/logout.db">Logout</a></li>
</ul>
</div>
<?
}
?>
<div class="navibox">
<h2>Tags</h2>
<p class="tag_links">
<?
 $tags = $Blog->get_tags();
 if(empty($tags))
	echo "keine Tags gefunden";
 else
 {
	foreach($Blog->get_tags() as $_tag)
	{
		echo "<a href=\"{$Blog->rootpath}/tag/{$_tag}\">{$_tag}</a> ";
	}
 }
?>
</p>
</div>
<div class="navibox">
<h2>Blogroll</h2>
<ul>
<!-- include Links from DB -->
<?php
 $Blog->fetch_blogroll();
?>
</ul>
</div>
 </div>
 <div id="content">
  <!-- Content -->
