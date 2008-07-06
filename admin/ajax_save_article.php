<?
header("Content-Type: application/json");

if(isset($_POST['js_save']) && $_POST['js_save'] == 'true')
{
  require 'core.php';
  if(!$Blog->user->is_online())
	die('{"status": "error", "code": 1006, "message": not logged in"}');
  if(!isset($_POST['article_id']) || !isset($_POST['article_title']) || !isset($_POST['article_writer']) || !isset($_POST['article_content']) || !isset($_POST['article_date']))
	die('{"status": "error", "code": 1000}');
  $id = $Blog->DB->real_escape_string($_POST['article_id']);
  $title = $Blog->DB->real_escape_string($_POST['article_title']);
  $shorted = $Blog->shorttext($title);
  $writer = $Blog->DB->real_escape_string($_POST['article_writer']);
  $content = $Blog->DB->real_escape_string($_POST['article_content']);
  $date = $Blog->DB->real_escape_string($_POST['article_date']);

  if(empty($content))
    die('{"status": "error", "code": 1001}');

  if(empty($title))
    $title = '<empty>';

  if($id == 0)
  {
    $sql = "INSERT INTO {news} (`created`, `short_name`, `title`, `writer`, `bb_code`, `ajax_saved`) VALUES ('{$date}', '{$shorted}', '{$title}', '{$writer}', '{$content}', '1')";
    $res = $Blog->query($sql) or die('{"status": "error", "code": 1002, "message": "'.$Blog->error().'"}');

    $sql = "SELECT id FROM {news} WHERE ajax_saved = '1' AND created = '{$date}' LIMIT 1";
    $res = $Blog->query($sql) or die('{"status": "error", "code": 1004, "message": "'.$Blog->error().'"}');
    if($res->num_rows <= 0) die('{"status": "error", "code": 1005}');
    $result = $res->fetch_object();
    $id = $result->id;
  }
  else
  {
    $sql = "UPDATE {news} SET `short_name` = '{$shorted}', `title` = '{$title}', `writer` = '{$writer}', `bb_code` = '{$content}' WHERE `id` = '{$id}'";
    $res = $Blog->query($sql) or die('{"status": "error", "code": 1003, "message": "'.$Blog->error().'"}');
  }
  
  // alles ok
  echo '{"status": "ok", "id": "'.$id.'", "date": "'.date('d.m.Y - H:i').'"}';
  exit(0);
}
else
{
 die('{"status": "error", "code": 1000}');
}
?>
