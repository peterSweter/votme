<?php
	class ActionHandler{

		public $ok_msg="";
		public $error_msg="";

		public function AddOK($string){
			$this->ok_msg.=$string;
			$this->ok_msg.="</br>";
		}

		public function AddError($string){
			$this->error_msg.=$string;
			$this->error_msg.="</br>";
		}

		public function ShowMSG(){

			if($this->ok_msg !=""){
				echo "<div class='ok_box'> $this->ok_msg </div>";
			}

			if($this->error_msg !=""){
				echo "<div class='error_box'> $this->error_msg </div>";
			}

		}
	}
?>