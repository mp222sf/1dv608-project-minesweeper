<?php 

class LayoutView {
	public function render($gvHTML) {
	    echo '<p></p><!DOCTYPE html>
	    
  			<html>
	        	<head>
	          		<meta charset="utf-8">
	          		<title>Minesweeper Project</title>
	          		<link rel="stylesheet" type="text/css" href="view/gameStyle.css">
	        	</head>
	        	<body>

	          		<h1>MINESWEEPER PROJECT</h1>
	          
	          		<div id="container">'
	          			. $gvHTML . '
	          		</div>
         		</body>
	      	</html>
	    ';
	  }
}