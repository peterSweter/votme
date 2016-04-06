<?php

	//error_reporting(E_ALL);
	ini_set("display_errors", 1);

	include_once("login.php");
	include_once("ActionHandler.php");
	include_once("folder.php");
	include_once("Photo.php");
	include_once("Price.php");
	include_once("Instructions.php");

	
	$login = new Login();
	$login->CheckSession();

	if($_GET['log_out']==1){
		$login->LogOut();
	}

	$login->DataBaseConnect();

	$folder = new Folder();
	$photo = new Photo();
	$price = new Price();
	$instructions =  new Instructions();

	$price->CreatePriceImg(1,20,40);

	//obsluga POST formularza

	switch($_POST['post_state']){
		case "new_folder":
			// dodawanie nowego folderu
				$folder->HandlePost($_POST['name']);	
			break;
		
		case 'new_photo':
			//dodawanie nowego zdjęcia

			$photo->PostHandle();

			break;

		case 'new_price':

			$price->PostHandle();

			break;
		case 'price_delete':

			$price->Delete();

			break;
		case 'photo_delete':

			$photo->Delete();
			
			break;
		case 'folder_rename':

			$folder->Rename();

			break;
		case 'folder_delete':

			$folder->Delete();
			
			break;
		default:

			break;
	}

?>

 <!DOCTYPE html>
<html>
<head>
	
	<meta charset="UTF-8">
	<title>Panel foto</title>
	<link rel="stylesheet" href="style.css">
	<script src="js/jquery.js"></script>
	<script src="js/script.js"></script>

</head>

<body>
	<div class="head_box">

		<div style="float:left"> &nbsp; &nbsp;<?php echo "Witaj ".$_SESSION['user']." w panelu do zarządzania zdjęciami "; ?>
			&nbsp; <a href="panel.php" style="font-weight:bold;"> Instrukcjia obsługi panelu.</a>
		</div>
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?log_out=1"> Wyloguj się </a>&nbsp; &nbsp;
	</div>
	<div id="foldery">
		<div class="foldery_head">
			
			<a href="panel.php?action=new_photo" style="float:right;margin-right:15px;"><div class="button"> nowe zdjęcie </div></a>
			<a href="panel.php?action=new_price" style="float:right;margin-right:5px;"><div class="button"> nowa cena </div></a>
			<a href="panel.php?action=new_folder" style="float:right;margin-right:5px;"><div class="button"> nowy folder</div></a>
			
			<div style="clear:both;"></div>
		</div>

		<div id="foldery_scroll">
			<div id="foldery_scroll_in">
				<?php $folder->FolderListShow(); ?>
			</div>
		</div>
	</div>
	<div id="content">
		<?php
			#obsluga $_GET['action'] - stanu w ktorym znajduje się panel

			switch($_GET['action']){
				case "new_folder":
					// wyswietl formularz nowego folderu
					$folder->ShowMSG();
					$folder->ShowNewFolderForm();
					
					break;
				case "new_photo":
					//wyswietl formularz nowego zdjecia

					$photo->ShowForm();


					break;
				case "new_price":
					// wyswietl formularz nowej ceny

					$price->ShowForm();

					break;
				case "show_price":
					// wyswietl cene

					$price->ShowPrice();

					break;
				case "show_photo":
					//wyswietl zdjecia + linki

					$photo->ShowPhoto();

					break;
				case "edit_folder":

					$folder->ShowEditForm();

					break;
				default:
					// wyswietl ekran instrukcji
						if($_GET['msg'] =="price_delete_msg"){
							$folder->AddOK("Pomyślnie usunięto cenę.");
						}

						if($_GET['msg'] =="photo_delete_msg"){
							$folder->AddOK("Pomyślnie usunięto zdjęcie.");
						}

						if($_GET['msg']=="folder_delete"){
							$folder->AddOK("Pomyślnie usunięto folder oraz jego zawartość");
						}

						$folder->ShowMSG();

						echo "<h2>Strona główna / Instrukcja</h1>";
						$instructions->ShowInstructions();
					break;
			}
		?>
	</div>
</body>

</html> 