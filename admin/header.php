<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
<head>
 <title>Admin Panel | <?=$Blog->settings['Blogname']; ?></title>
 <base href="<?=$Blog->adminrootpath ?>/" />
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/main.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/style.css" media="screen" />
<? if(isset($_GET['site']) && $_GET['site'] == 'formular') { ?>
 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/editor.css" media="screen" />
 <script type="text/javascript" src="<?=$Blog->adminrootpath; ?>/js/editor.js"></script>
 <script type="text/javascript" src="<?=$Blog->adminrootpath; ?>/js/prototype.js"></script>
<? } ?>
<? if(isset($_GET['site']) &&  $_GET['site'] == 'pages_formular') { ?>
 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/editor.css" media="screen" />
 <script type="text/javascript" src="<?=$Blog->adminrootpath; ?>/js/editor.js"></script>
<? } ?>
</head>
<? if(isset($_GET['site']) && $_GET['site'] == 'formular' && $Blog->settings['AJAX-Autosave'] == 'an') { ?>
<body onload="editorInit();">
<? } else { ?>
<body>
<? } ?>
<div id="wrap">
 <div id="navigation">
<ul>
 <li>Hallo, <?=$Blog->user->name ?></li>
 <li>[<a href="<?=$Blog->rootpath; ?>">zur Seite</a>]</li>
 <li>[<a href="<?=$Blog->adminrootpath; ?>/logout.db">Logout</a>]</li>
</ul>
<br />
<ul>
 <li<?= $cur_site == 'overview' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/">Übersicht</a></li>
 <li<?= $cur_site == 'formular' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/formular.db/0/new">Artikel eintragen</a></li>
 <li<?= $cur_site == 'article' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/article.db">Artikel</a></li>
 <li<?= $cur_site == 'comments' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/comments.db">Kommentare</a></li>
 <li<?= $cur_site == 'pages' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/pages.db">Unterseiten</a></li>
 <li<?= $cur_site == 'links' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/links.db">Links</a></li>
 <li<?= $cur_site == 'user' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/user.db">User</a></li>
 <li<?= $cur_site == 'settings' ? ' class="active_menu"' : ''; ?>><a href="<?=$Blog->adminrootpath; ?>/settings.db">Einstellungen</a></li>
</ul>
 </div>
 <div id="content">
