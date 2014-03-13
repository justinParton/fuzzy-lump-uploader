<?php

 
	function colorize($text, $status) {

			$out = "";
			 switch($status) {
			  case "INFO":
			   $out = "[1;33m"; //Yellowbackground
			   break;
			  case "STATUS":
			   $out = "[1;35m"; //Purple background
			   break;
			  case "WARNING":
			   $out = "[1;31m"; //RED background
			   break;
			  case "NOTE":
			   $out = "[34m"; //Blue background
			   break;
			  default:
			   throw new Exception("Invalid status: " . $status);
			 }
			 return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }	
?>
