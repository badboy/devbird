<?
$cur_title = 'Login';
if(isset($_POST['user_login']))
{
	$username = $_POST['user_name'];
	$password = $_POST['user_password'];
	if(empty($username) || empty($password))
 	{
		$error_msg = "Bitte alle Felder ausfüllen!";
	}
	else
	{
		$Blog->user = User::find_by_name($username);
		var_dump($Blog->user);
		if($Blog->user && $Blog->user->login($password))
		{
			$Blog->user->is_online();
			header("Location: {$Blog->adminrootpath}");
		}
		else
		{
			$error_msg = "Login-Daten waren nicht richtig. Versuche es noch einmal.";
		}
	}
}

include 'header_user.php';
echo "<h1>{$cur_title}</h1>\n";

if(!empty($error_msg)) {
?>
<form action="<?=$_SERVER['REQUEST_URI'] ?>" method="post">
 <fieldset>
  <legend>Meldung</legend>
  <p style="text-align:center;color:red;"><strong><?=$error_msg ?></strong></p>
 </fieldset>

<?
}
?>
<form action="<?=$_SERVER['REQUEST_URI'] ?>" method="post">
 <fieldset>
  <legend>Login</legend>
  <p><input type="text" name="user_name" id="username" /> Username</p>
  <p><input type="password" name="user_password" /> Passwort </p>
  <br />
  <p><input type="submit" value="Login" name="user_login" /></p>
 </fieldset>
</form>
