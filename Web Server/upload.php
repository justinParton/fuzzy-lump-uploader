<?php
	// Settings
	$tmpDir = realpath('../audio/sermon/video/tmp').DIRECTORY_SEPARATOR;

	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 106000; // Temp file age in seconds
	$chunkTotal = ($arguments['chunk_total'] - 1);
	$chunkSize = $arguments['chunk_size'];
	$chunkID = $arguments['chunk_id'];
	$fileSize = $arguments['total_size'];
	$fileEnding = $arguments['file_ext'];
	$outputPathCount = 1;
	$recordLimit = true;	

	switch($fileEnding) {
			case '.mp3':
				$outputDir = '../audio/sermon/audio/';
				break;
			case '.mp4':
				$outputDir = '../audio/sermon/video/';
				break;
		}

	$outputPath = realpath($outputDir).DIRECTORY_SEPARATOR.date('Y-md').$fileEnding;
	if($chunkID == 0){

		$query = "SELECT * FROM sermons WHERE admin ORDER BY id ASC";		
		if ($stmt = query($query)) {
			while($row = mysqli_fetch_array($stmt, MYSQLI_ASSOC)){
			$rows[]=$row;
			}
			if(count($rows)>= 4 && $recordLimit == true){
				if(unlink('../audio/sermon/audio/'.basename($rows[0]['audio_url'])) && unlink('../audio/sermon/video/'.basename($rows[0]['video_url']))){
					$id=$rows[0]['id'];
					$query = "DELETE FROM sermons WHERE id =".$id;
					if ($stmt = query($query)) {
					}
				}
				else{
					print_r(error_get_last());
				}
			}
		}
		mysqli_close($link);
		while(file_exists($outputPath)){
			$outputPath = realpath($outputDir).DIRECTORY_SEPARATOR.date('Y-md').$outputPathCount.$fileEnding;
			$outputPathCount++;
		}
	}
	
	if (!file_exists($tmpDir)) {
		@mkdir($tmpDir);
	}

	$filePath = $tmpDir . DIRECTORY_SEPARATOR . $fileName;

		$out = fopen($outputPath, "ab");
		$in = fopen($_FILES["file_data"]["tmp_name"],'rb');
		$buff = fread($in,$chunkSize);
		fwrite($out,$buff);

	$CLI_MODE = true;

	if($CLI_MODE){
		if($chunkID == 0){
			echo "\tUploaded:        ";
		}
	    $perc = number_format((($chunkID/$chunkTotal) * 100),1);
		$num = strlen($perc) + 2;
		echo "\033[{$num}D";
		echo str_pad($perc.'%', 3, '',STR_PAD_LEFT) . ' ';
	}
?>
