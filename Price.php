<?php
	class Price extends ActionHandler{

		public $target_dir = "files/";

		public function ShowForm(){

			$query = "SELECT * FROM foldery";
			$r = mysql_query($query);

			$html_select = "";

			while($row = mysql_fetch_array($r)){
				$html_select.="<option value='".$row['id']."'>".$row['name']."</option>";
			}			

			$this->ShowMSG();

			$html =<<< EOHTML
			<h2> Dodawanie nowej ceny </h2>

			<form action="panel.php?action=new_price" method='POST'>
				nazwa: <input type="text" name="name"> </br>
				Wybierz folder dla ceny: <select name="select_folder"> $html_select </select> </br>
				stara cena: <input type="text" name="old_price"> </br>
				nowa cena: <input type="text" name="new_price"> </br>
				<input type="hidden" name="post_state" value="new_price">
				<input type="submit" value="dodaj cene">
			</form>

EOHTML;
			echo $html;
		}

		public function FindLastId(){

			$query = "SELECT * FROM price ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query);

			if($r_tab = mysql_fetch_array($result)){
				return $r_tab['id'];
			}else{
				return 0;
			}

		}

		public function CreatePriceImg($new_id, $new_price, $old_price){

			$width = 180;
			$height =130;
			$font_file = './arial.ttf';
			$percent = floor((($old_price-$new_price)/$old_price)*100);

			$tmp_img = imagecreatetruecolor($width, $height);
			$green = imagecolorexact($tmp_img, 146, 252, 7);
			$black = imagecolorexact($tmp_img, 0, 0, 0);
			$white = imagecolorexact($tmp_img, 255, 255, 255);
			$red = imagecolorexact($tmp_img, 255, 0,0 );
			$yellow = imagecolorexact($tmp_img, 255, 255,0 );

			imagefilledrectangle($tmp_img, 0 , 0,$width, $height, $yellow);
			imagefilledrectangle($tmp_img, 0 , 0,$width, 40, $red);
			imagefilledrectangle($tmp_img, 0 , $height-20,$width, $height, $red);

			

			imagefttext($tmp_img, 34, 0, 40, 38, $white, $font_file, "-".$percent."% ");
			imagefttext($tmp_img, 48, 0, 40, $height-25, $black, $font_file, $new_price);
			imagefttext($tmp_img, 11, 0, 70, 54, $black, $font_file, "stara cena:".$old_price);
			imagefttext($tmp_img, 14, 0, 45, $height-4, $white, $font_file, "Promocja!");


			imagejpeg($tmp_img, $this->target_dir.$new_id."p.jpg");


		}

		public function PostHandle(){
			// pobieranei danych i tworzenie grafiki do folderu files

			if($_POST['name']==""){
				$this->AddError("Błąd dodawania ceny, Podaj nazwę ceny!");
				return false;
			}

			if($_POST['select_folder']==""){
				$this->AddError("Nie wybrano folderu, stwórz folder aby dodawać do niego zawartość");
				return false;
			}
			//sprawdzenie czy istnieje juz obrazek o takiej nazwie w folderze
			$query = "SELECT * FROM price WHERE id_dir = {$_POST['select_folder']} AND name ='{$_POST['name']}' ";
			$result = mysql_query($query);

			
			if(mysql_fetch_array($result)){
				$this->AddError("BŁĄD! W wybranym folderze istnieje już cena o takiej samej nazwie.");
				return false;
			}




			$query = "INSERT INTO price (name, id_dir) VALUES ('{$_POST['name']}', {$_POST['select_folder']})";
			mysql_query($query);

			$new_id = $this->FindLastId();

			$this->CreatePriceImg($new_id, $_POST['new_price'], $_POST['old_price']);

			$this->AddOK("Pomyślnie dodano cenę");


		}

		public function ShowPrice(){

			$id= $_GET['id_price'];

			$query = "SELECT * FROM price WHERE id=$id";
			$result= mysql_query($query);
			$row = mysql_fetch_array($result);

			if(!$row)return false;

			$html=<<< EHTML
			<h2> Wyświetlanie ceny {$row['name']} </h2>
			<img src="files/{$id}p.jpg"></br>
			link do obrazka z ceną:<br>
			www.votme.pl/files/{$id}p.jpg
			
			</br></br></br>
			<span style="font-weight:bold">Usuń tą cene</span></br>
			<form action="panel.php?action=show_price&id_price=$id" method="POST">
				Chcę usunąć tą cenę <input type="checkbox" name="delete_check" value="0"></br>
				<input type="hidden" name="post_state" value="price_delete">
				<input type="hidden" name="price_id" >
				<input type="submit" value="usuń tą cenę">
			</form>
EHTML;
		$this->ShowMSG();
		echo $html;

		


		}

		public function Delete(){
			
			$id= $_GET['id_price'];
			$query = "DELETE FROM price WHERE id=$id";

			if($_POST['delete_check']==='0'){
				mysql_query($query);
				unlink("files/{$id}p.jpg");
				header("Location: panel.php?msg=price_delete_msg");


			}else{
				$this->AddError('Zaznacz pole "Chcę usunąć tą cenę" aby usunąć tą cenę!');

			}
		}


	}
?>