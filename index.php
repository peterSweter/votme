<?php

 	error_reporting(E_ALL);
 	include_once("login.php");
	
	
	$login = new Login();
	$login->DataBaseConnect(); 

	if(isset($_POST['form_control'])){

		$login->UserCheck($_POST['login'], $_POST['password']);
	}

	session_start();
	if($_SESSION['init']){
		$login->CheckSession();
		header("Location: panel.php");
	}

?>

 <!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Logowanie - panel foto </title>
	<link rel="stylesheet" href="style.css">
</head>

<body>

	
		<?php echo $login->PrintErrors(); ?>
	
	
	<div id="login_box">

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

			</br>
			<h1> Logowanie do panelu foto </h1>
			</br>
			login: <input type="text" name="login"/> </br></br>
			hasło: <input type="password" name="password" /> </br></br>
			<input type="hidden" name="form_control" value="1"/>
			<input type="submit" value="zaloguj"/>
		
		</form>

	</div>

</body>

</html> 