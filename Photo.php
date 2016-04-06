<?php
	class Photo extends ActionHandler{

		public $target_dir = "files/";

		public function ShowForm(){

			$query = "SELECT * FROM foldery";
			$r = mysql_query($query);

			$html_select = "";

			while($row = mysql_fetch_array($r)){
				$html_select.="<option value='".$row['id']."'>".$row['name']."</option>";
			}			

			$this->ShowMSG();



			$html =<<< EOHT
			<form action="panel.php?action=new_photo" method="post" enctype="multipart/form-data">
			    <h2> Wybierz zdjęcie do dodania </h2> 
			    Wybierz folder dla zdjęcia: <select name="select_folder"> $html_select </select> </br>
			    <input type="file" name="fileToUpload" id="fileToUpload"> </br>
			    <input type="hidden" name="post_state" value="new_photo">
			    <input type="submit" value="Dodaj zdjęcie" name="submit">
			</form>

		

EOHT;
			
			echo $html;

		}

		public function FindLastId(){

			$query = "SELECT * FROM photo_group ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query);

			if($r_tab = mysql_fetch_array($result)){
				return $r_tab['id'];
			}else{
				return 0;
			}

		}

	
		public function ImageHandle($orginal_image, $group_photo_id, $imageFileType){

			// resize image and save as 1r.jpp

			$new_width = 456;
			$new_height = 625;
			$font_file = './arial.ttf';



			$tmp_img = imagecreatetruecolor($new_width, $new_height);
			$source  = imagecreatefromjpeg($orginal_image);
			$green = imagecolorexact($tmp_img, 146, 252, 7);
			$black = imagecolorexact($tmp_img, 0, 0, 0);

			list($width, $height) = getimagesize($orginal_image);
			imagecopyresized($tmp_img, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			$resized_image_target = $this->target_dir.$group_photo_id."r.".$imageFileType;
			
			imagejpeg($tmp_img, $resized_image_target);

			// creating image with caption

			$caption_image_target = $this->target_dir.$group_photo_id.".".$imageFileType;

			
			imagefilledrectangle($tmp_img, 0 , $new_height - 60,$new_width, $new_height, $green);
			imagefttext($tmp_img, 47, 0, 100, $new_height-8, $black, $font_file, 'TANIO !');

			imagejpeg($tmp_img, $caption_image_target);

			$query = "INSERT INTO photo ( id_group_photo, path, type) VALUES ($group_photo_id,'$resized_image_target',2)";
			mysql_query($query);

			$query = "INSERT INTO photo ( id_group_photo, path, type) VALUES ($group_photo_id,'$caption_image_target',3)";
			mysql_query($query);

		}

		public function smallerOrginal($orginal_image){
			$new_width = 450;
			$new_height = 625;
			$font_file = './arial.ttf';



			$tmp_img = imagecreatetruecolor($new_width, $new_height);
			$source  = imagecreatefromjpeg($orginal_image);
			$green = imagecolorexact($tmp_img, 146, 252, 7);
			$black = imagecolorexact($tmp_img, 0, 0, 0);

			list($width, $height) = getimagesize($orginal_image);
			imagecopyresized($tmp_img, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

			return $tmp_img;
		}

		public function PostHandle(){

			$new_photo_group_id = $this->FindLastId()+1;

			$target_dir = $this->target_dir;
			

			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			$target_old_file = $target_dir.$new_photo_group_id."o.".$imageFileType;
			$group_name = basename($_FILES["fileToUpload"]["name"]);
			

			$uploadOk = 1;
			
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
			    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			    if($check !== false) {
			       
			        $uploadOk = 1;
			    } else {
			        $uploadOk = 0;
			        $this->AddError("Błąd dodawania, wysłany plik nie jest obrazem,  spróbuj ponownie.");
			    }
			}

			// limit typów zdjęć 
			if($imageFileType != "jpg"  && $imageFileType != "jpeg"  ) {
			    $uploadOk = 0;
				$this->AddError("Błąd dodawania, wysyłane zdjęcie musi mieć rozszerzenie .jpg lub .jpeg");
			} 

			// check if in folder is already fille with the same name

			$query = "SELECT * FROM  photo_group WHERE name = '$group_name' AND id_dir = {$_POST['select_folder']}";

			$r=mysql_query($query);

			if(mysql_fetch_array($r)){
				
				$uploadOk =0;
				$this->AddError("Błąd dodawania, w wybranym wolderze istnieje już zdjęcie o takiej nazwie.");

			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
			    $this->AddError("Błąd dodawania!");
			// if everything is ok, try to upload file
			} else {
				//move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_old_file)
			    if ( imagejpeg($this->smallerOrginal($_FILES["fileToUpload"]["tmp_name"]), $target_old_file, 70)){

			    	$this -> ImageHandle($target_old_file, $new_photo_group_id, $imageFileType);

			    	$query = "INSERT INTO photo_group (id, name, id_dir) VALUES ($new_photo_group_id,'$group_name', {$_POST['select_folder']} )";
			    	mysql_query($query);

			    	$query = "INSERT INTO photo ( id_group_photo,path, type) VALUES ($new_photo_group_id,'$target_old_file',1)";
			    	mysql_query($query);

			        $this->AddOK("Zdjęcie ". basename( $_FILES["fileToUpload"]["name"])." zostało pomyślnie dodane" );
			    } else {
			        $this->AddError("Błąd dodawania! #server_fail");
			    }
				
			}

		}

		public function ShowPhoto(){

			$id_group_photo = $_GET['id_group_photo'];

			$query = "SELECT * FROM photo_group WHERE id='$id_group_photo'";
			$result = mysql_query($query);
			$group_row = mysql_fetch_array($result);

			$query = "SELECT * FROM photo WHERE id_group_photo='$id_group_photo' ORDER BY type DESC";
			
			$result = mysql_query($query);


			
			$caption_img_row =mysql_fetch_array($result);
			$resized_img_row = mysql_fetch_array($result);
			$old_img_row = mysql_fetch_array($result);
			
			if(!$old_img_row)return false;
			$this->ShowMSG();
			echo "<h2> Wyświetlanie zdjęcia ".$group_row['name']."</h2>";

			$html =<<< EHTML

			<div class="foto_box">
				Zdjęcie z napisem "Tanio": </br>
				<img src="{$caption_img_row['path']}"/></br>
				link do zdjęcia:</br>
				www.votme.pl/{$caption_img_row['path']}

			</div>

			<div class="foto_box">
				Zdjęcie bez napisu: </br>
				<img src="{$resized_img_row['path']}"/></br>
				link do zdjęcia:</br>
				www.votme.pl/{$resized_img_row['path']}

			</div>
			

			<div style="clear:both"></div>
			</br></br>
			<h3> Link do oryginalnego zdjęcia:</h3>
			www.votme.pl/{$old_img_row['path']}

			</br></br></br>
			<span style="font-weight:bold">Usuń to zdjęcie</span></br>
			<form action="panel.php?action=show_photo&id_group_photo=$id_group_photo" method="POST">
				Chcę usunąć to zdjęcie <input type="checkbox" name="delete_check" value="0"></br>
				<input type="hidden" name="post_state" value="photo_delete">
				<input type="hidden" name="id_group_photo" >
				<input type="submit" value="usuń to zdjęcie">
			</form>


EHTML;
			
			echo $html;
		}

		public function Delete(){
			
			$id= $_GET['id_group_photo'];
			
			$query = " DELETE FROM photo_group WHERE id=$id ;";
			$query_2 = "DELETE FROM photo WHERE id_group_photo=$id";

		

			if($_POST['delete_check']==='0'){
				mysql_query($query);
				mysql_query($query_2);
				unlink("files/{$id}r.jpg");
				unlink("files/{$id}o.jpg");
				unlink("files/{$id}.jpg");

				header("Location: panel.php?msg=photo_delete_msg");


			}else{

				$this->AddError('Zaznacz pole "Chcę usunąć to zdjęcie" aby usunąć to zdjęcie!');

			}
		}

	}
?>