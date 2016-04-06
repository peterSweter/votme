<?php
	class Folder extends ActionHandler {

		public function FolderListShow(){
			
			$query = "SELECT * FROM foldery";
			$result = mysql_query($query);


			while($r = mysql_fetch_array($result)){

				echo"<div class='folder_tab'>".$r['name']."</div>";	
				
				$query = "SELECT * FROM  photo_group WHERE id_dir='{$r['id']}'";
				$result_file = mysql_query($query);

				echo "<div class='files_box'> <ul class='photo_ul'>";

				$i=0;

				while($row_file = mysql_fetch_array($result_file)){

					echo "<li><a href='panel.php?action=show_photo&id_group_photo={$row_file['id']}'> {$row_file['name']} </a> </br> ";
					$i++;
				}

				

				

				echo "</ul><ul class='price_ul'>";

				$query = "SELECT * FROM  price WHERE id_dir='{$r['id']}'";
				$result_file = mysql_query($query);

				while($row_file = mysql_fetch_array($result_file)){

					echo "<li><a href='panel.php?action=show_price&id_price={$row_file['id']}'> {$row_file['name']} </a> </br> ";
					$i++;
				}
				if($i==0) echo "pusty folder </br>";

				$html =<<<EHTML
				<a href="panel.php?action=edit_folder&folder_id={$r['id']}" class="dir_button">
					<img src="img/settings.png">
					&nbsp; edytuj ten folder
				</a>
EHTML;

				echo "</ul></br> $html </div>";


				





			}
		}

		public function ShowNewFolderForm(){



			$html =<<< EOHTML
				<h2> Dodaj nowy folder: </h2>
				<form action="panel.php?action=new_folder" method="POST">
					Nazwa folderu: <input type="text" name="name"/> </br>
					<input type="hidden" name="post_state" value="new_folder"/>
					<input type="submit" value="Dodaj folder"/>
				</form> 

EOHTML;

			echo $html;

		}

		public function HandlePost($name){

			$query = "SELECT * FROM foldery WHERE name='$name'";
			$result = mysql_query($query);

			if(mysql_fetch_array($result)){

				$this->AddError("Istnieje już folder o takiej nazwie");

			}else{

				$query = "INSERT INTO foldery (name) VALUES ('$name')";

				if(mysql_query($query)){

					$this->AddOK("Pomyślnie utworzono folder.");
				}else{

					$this->AddError("Błąd bazy danych");
				}

			}

		}

		public function ShowEditForm(){

			$this->ShowMSG();

			$query = "SELECT * FROM foldery WHERE id={$_GET['folder_id']}";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);

			if(!$row)return false;

			$html=<<< EOHTML
			<h2>Edycja folderu {$row['name']}:</h2> 
			</br>
			<h4> Zmień nazwę folderu:</h4>
			<form action="panel.php?action=edit_folder&folder_id={$_GET['folder_id']}" method="POST">
				nowa nazwa: <input type="text" name="name" value="{$row['name']}"></br>
				<input type="hidden" name="post_state" value="folder_rename">
				<input type="submit" value="zmień nazwę">

			</form>

			</br></br></br>

			<h5> Usuń folder i wszystkie zdjęcia oraz ceny w nim zawarte:</h5>
			<span style="color:red; font-weight:bold">Uwaga! usunięcie fodleru spowoduje usunięcie jego zawartości.</span>
			<form action="panel.php?action=edit_folder&folder_id={$_GET['folder_id']}" method="POST">
				zaznacz aby usunąć folder: <input type="checkbox" value="1" name="check_1"></br>
				<input type="hidden" name="post_state" value="folder_delete">
				<input type="submit" value="usuń ten folder">
			</form>

EOHTML;
			
			echo $html;
		}

		public function Rename(){

			if($_POST['name']==""){
				$this->AddError("Błąd zmiany nazwy, nie podano nowej nazwy!");
				return false;
			}

			$query = "SELECT * FROM foldery WHERE name='{$_POST['name']}' AND id!={$_GET['folder_id']}";
			$result = mysql_query($query);

			if(mysql_fetch_array($result)){

				$this->AddError("Istnieje już folder o nazwie {$_POST['name']} podaj inną nazwę!");
				return false;
			}

			$query = "UPDATE foldery SET name ='{$_POST['name']}' WHERE id={$_GET['folder_id']}  ";
			mysql_query($query);

			$this->AddOK("Pomyślnie zmieniono nazwę folderu.");
		}

		public Function Delete(){

			if($_POST['check_1']!=1){
				$this->AddError("Niezaznaczono dodatkowego pola przy usuwaniu, błąd usuwania!");
				return false;
			}

			$id = $_GET['folder_id'];

			$query = "SELECT * FROM photo_group WHERE id_dir = $id ";
			$result = mysql_query($query);

			while($row = mysql_fetch_array($result)){

				$id_group_photo = $row['id'];
				unlink("files/{$id_group_photo}.jpg");
				unlink("files/{$id_group_photo}r.jpg");
				unlink("files/{$id_group_photo}o.jpg");

				$query_w = "DELETE FROM photo WHERE id_group_photo='{$id_group_photo}' ";
				mysql_query($query_w);

				$query_w = "DELETE FROM photo_gorup WHERE id ='{$id_group_photo}' ";

				mysql_query($query_2);
			}

			$query = "SELECT * FROM price WHERE id_dir = $id ";
			$result = mysql_query($query);

			while($row = mysql_fetch_array($result)){
				$id_price = $row['id'];
				unlink("files/{$id_price}p.jpg");
				$query_w = "DELETE FROM price WHERE id ='{$id_price}' ";
				mysql_query($query_w);

			}

			$query= " DELETE FROM foldery WHERE id= $id";
			mysql_query($query);



			header("Location: panel.php?msg=folder_delete");


		}
	}	
?>