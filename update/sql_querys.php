<?
/*
 * FIXME
 * need to be edited
 * to match v0.4.0 style ;9
 */

$edit_tables  = array();
$edit_tables[] = <<<QUERY
DELETE FROM {$db_prefix}settings WHERE `name` = 'Extra-BB-Codes' LIMIT 1 ;
QUERY;

$edit_tables[] = <<<QUERY
UPDATE {$db_prefix}settings SET `sort` = '8' WHERE `name` = 'AJAX-Autosave' LIMIT 1 ;
QUERY;

$edit_tables[] = <<<QUERY
UPDATE {$db_prefix}settings SET `sort` = '9' WHERE `name` = 'Standardcodesprache' LIMIT 1 ;
QUERY;

?>
