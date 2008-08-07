<?php
require 'core.php';
if(isset($_GET['type']) && $_GET['type'] === 'atom')
	$Blog->atomfeed();
else
	$Blog->rssfeed();
?>
