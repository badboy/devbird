<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="de">
<head>
 <title>Admin Panel | <?=$Blog->settings['Blogname']; ?></title>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/main.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="<?=$Blog->adminrootpath; ?>/css/style.css" media="screen" />
</head>
<body<?=($cur_title=='Login' ? ' onload="if(document.getElementById(\'username\')) document.getElementById(\'username\').focus();"' : '')?>>
<div id="wrap">
 <div id="navigation">
<ul>
 <li>[<a href="<?=$Blog->rootpath; ?>">zur Seite</a>]</li>
 <li>[<a href="<?=$Blog->adminrootpath; ?>/login.db">Login</a>]</li>
</ul>
 </div>
 <div id="content">
