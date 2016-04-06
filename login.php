<?php
	class Login{

		private $mysql_hostname = '*clasified*';
		private $mysql_user ='*clasified*';
		private $mysql_password ='*clasified*';
		private $mysql_database_name = '*clasified*';

		public $error_message="";
		private $login_time_limit=3600;

		public function DataBaseConnect(){

			mysql_connect($this->mysql_hostname, $this->mysql_user, $this->mysql_password) or die ("blad polaczenia z baza danych #1");
			mysql_select_db($this->mysql_database_name) or die ("blad polaczenia z baza danych #2");

		}

		public function UserCheck($login, $password){

			$login = addslashes(trim($login));
			$password = addslashes(trim($password));

			$query = "SELECT * FROM users WHERE login='$login' AND password ='$password' ";
			$results = mysql_query($query);

			if($query_user = mysql_fetch_array($results)){

				$this->RegisterSession($_POST['login']);
				header('Location: panel.php');

			}else{

				$this->error_message .= " Zły login lub hasło, spróbuj ponownie.  <br>";
			}


		}

		public function PrintErrors(){

			if($this->error_message != ""){
				echo "<div class='error_box'>".$this->error_message."</div>";
			}
		}

		private function RegisterSession($user_name){

			session_start();

			if(!isset($_SESSION['init'])){
				session_regenerate_id();
				$_SESSION['init'] = true;
				$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['user'] = $user_name;
			}
		}

		public function CheckSession(){

			session_start();

			if(!isset($_SESSION['init']) or $_SESSION['ip'] != $_SERVER['REMOTE_ADDR']){

				$this->LogOut();

			}
		}

		public function LogOut(){
			session_destroy();
			header('Location: index.php');
		}





	}
?>
