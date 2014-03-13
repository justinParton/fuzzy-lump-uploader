<?php
/*
date_default_timezone_set('America/Chicago');

Title: Vimeo PHP Uploader
Description: Uploads videos to A server using chunk segmentation process.
*/

date_default_timezone_set('America/Chicago');
include 'includes\functions.php';
include('includes\chunker.php');
$A = new GBFIC();

//Configuration Settings
//notify
$username = 'EMAIL_ADDRESS';
$password = 'EMAIL_PASSWORD';
$smtpServer = 'SMTP_ADDRESS';
$sender = 'RECIEVING_EMAIL_ADDRESS';
$reciever = 'PHONE_NUMBER@CARRIER';
$FILE_PATH = 'FINAL_PATH_TO_FILE_ON_WEBSERVER' // Such as: http://example.com/audio/

//END Configuration


    // Enable this if you want to check for existing files		
	//$A->BckCheck($sender,$reciever,$username,$password,$smtpServer);
		
	echo '';
	fwrite(STDOUT, colorize("\n-------------------------------------------------------------------------------\n", "INFO"));
	fwrite(STDOUT, colorize("\n-                                                                             -\n", "INFO"));
	fwrite(STDOUT, colorize("\n-                        Broadcast Uploader                                   -\n", "INFO"));
	fwrite(STDOUT, colorize("\n-                                                                             -\n", "INFO"));
	fwrite(STDOUT, colorize("\n-                                                                             -\n", "INFO"));
	fwrite(STDOUT, colorize("\n-------------------------------------------------------------------------------\n", "INFO"));
		
	//User Information Inputs
	echo '  Title: ';
	$name = fgets(STDIN);
	echo '  Speaker: ';
	$author = fgets(STDIN);
	echo '';
	fwrite(STDOUT, colorize("Starting\n", "INFO"));
	
    $A->notify($sender,$reciever,$username,$password,$smtpServer,'alert-info','Starting VU','Starting VU Program');
	
	//Upload Section
	fwrite(STDOUT, colorize("Uploading Video\n", "INFO"));
	
	$A->notify(null,null,null,null,null,'alert-info',null,'Uploading Video');
	
	//Actual Upload Section
	$nameTag = date('Y-md');$pubDate = date('M j, Y');
	$A->chunky('ready.mp4','http://api.gbfic.org/sermon_upload');
	$A->chunky('audio-web.mp3','http://api.gbfic.org/sermon_upload');
	$A->dbify('sermons',$name,$author,$pubDate,$FILE_PATH.$nameTag.'.mp4',$FILE_PATH.$nameTag.'.mp3');
	// END Upload Section

	echo  colorize("Finished", "STATUS").PHP_EOL.PHP_EOL;
	$A->notify($sender,$reciever,$username,$password,$smtpServer,'alert-success','Upload Complete','Job Completed Successfully');
	
	function anykey($s='Press any key to continue...') {
		echo "\n".$s."\n";
		fgetc(STDIN);
		echo shell_exec('del *.mp3');
	}



?>
