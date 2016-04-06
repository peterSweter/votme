<?php 
	class Instructions{
		public function ShowInstructions(){
			$html =<<< EHTML

			<h4>Wideo instrukcja: </h4>
		<video width="720" height="480" controls>
			  <source src="video_tutorial.mp4"  type="video/mp4">
			  <source src="movie.ogg" type="video/ogg">
			  Your browser does not support the video tag.
			</video>

EHTML;
		
		echo $html;

		}
	}
?>