<?php   
class GBFIC{
	public function Cleanify($folder){
		$files = glob($folder);
		foreach($files as $file){
			if(is_file($file)){
				unlink($file);
			}
		}
	}
	public function bckCheck($sender,$reciever,$username,$password,$smtpServer){
             $existBackup = FALSE;
			 while(!file_exists('FILE_TO_UPLOAD')){
			    
				if(!$existBackup){
    			 fwrite(STDOUT, colorize("\n Backup.f4v is Present in Directory, Please Delete.\n\n", "INFO"));
				 $this->notify($sender,$reciever,$username,$password,$smtpServer,'alert-warning','Stopping','Please Delete The Backup File');
	
			     $existBackup = TRUE;

				 }
				 sleep(3);
			 }
			}
	public function notify($sender = null,$phone = null,$username = null,$password = null,$smtp = null,$process,$subject = null,$content,$progressTitle = null){
		echo shell_exec('sendEmail -q -f '. $sender .' -t '. $phone .' -xu '. $sender .' -xp '.$password.' -s '. $smtp .' -u $subject -m '. $content);
		$uname   = 'DB_USERNAME';
		$pwd   	 = 'DB_PASSWORD';
		$dbhost  = 'DB_URL';
		$db = 'DB_NAME';
		$con = mysqli_connect($dbhost,$uname,$pwd,$db);
		$sql = "INSERT INTO NOTIFICATION_TABLE_NAME (error,alert,title) VALUES ('$process','$content','$progressTitle')";
		if(!mysqli_query($con,$sql)){
		    echo 'Error: '.mysqli_error($con);
		};
	}
	public function dbify($table,$name = null,$author = null ,$date = null,$videoUrl = null,$audioUrl = null){
		
		$uname   = 'DB_USERNAME';
		$pwd   	 = 'DB_PASSWORD';
		$dbhost  = 'DB_URL';
		$db = 'DB_NAME';
		
		$con = mysqli_connect($dbhost,$uname,$pwd,$db);
		$sql = "INSERT INTO ". $table ." (admin,name,author,date,video_url,audio_url) VALUES (1,'$name','$author','$date','$videoUrl','$audioUrl')";
		if(!mysqli_query($con,$sql)){
		    echo 'Error: '.mysqli_error($con);
		};
	}
	public function actionCurl($url,$posts){
		
		// Post the file
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $posts);
		$rsp = curl_exec($curl);
		curl_close($curl);
		return $rsp;
	}
	public function dynAlert($string,$end = false,$flash = false){
		$num = strlen($string);
		$numBreak = $num +2;
		echo "\033[{$numBreak}D";
		if($end){$EOL = ''.PHP_EOL;}else{ $EOL = ' ';}
		echo str_pad($string, $num, '',STR_PAD_LEFT) . $EOL;
	}
	public function chunky($file,$url){
		date_default_timezone_set('America/Chicago');
		$endpoint = $url;
		//$endpoint = 'http://api.gbfic.org/video_up';
		$file_path = $file;
		$chunk_temp_dir = 'tmp';
		$size = 2097152;
		$replace_id = null;
		$ie = 0;
		if (!file_exists($file_path)) {
			die("This Video {$file_path} Doesn't Exist");
		}

		// Figure out the filename and full size
		$path_parts = pathinfo($file_path);
		$file_name = $path_parts['basename'];
		$file_size = filesize($file_path);
		
		$params = array();
		
		// Split up the file if using multiple pieces
		$chunks = array();
		$use_multiple_chunks = true;
		$number_of_chunks;
		if ($use_multiple_chunks) {
			if (!is_writeable($chunk_temp_dir)) {
				throw new Exception('Your Temp Directory is Not Accessible.');
			}

			// Create pieces
			$this->notify(null,null,null,null,null,'alert-info',null,'Chopping The Video');
			$number_of_chunks = ceil(filesize($file_path) / $size);
			for ($i = 0; $i < $number_of_chunks; $i++) {
				$chunkAmount = ($i/$number_of_chunks) * 100;
				$chunk_file_name = "{$chunk_temp_dir}/{$file_name}.{$i}";
				if($i == ($number_of_chunks - 1)){ $endEOL = true;}else{$endEOL = false;}
				// Break it up
				$this->notify(null,null,null,null,null,'progress-bar',null,$chunkAmount,'Chunks Created');
				$this->dynAlert('Chunk '.$i.' Created',$endEOL,false);
				$chunk = file_get_contents($file_path, FILE_BINARY, null, $i * $size, $size);
				$file = file_put_contents($chunk_file_name, $chunk);

				$chunks[] = array(
					'file' => realpath($chunk_file_name),
					'size' => filesize($chunk_file_name)
				);
			}
		}
		else {
			$chunks[] = array(
				'file' => realpath($file_path),
				'size' => filesize($file_path)
				
			);
		}
		
		// Upload each piece
		
		foreach ($chunks as $i => $chunk) {
				$chunkAmount = ($i/$number_of_chunks) * 100;

				$params = array(
					'chunk_total' => $number_of_chunks,
					'total_size' => $file_size,
					'chunk_size' => filesize($chunk['file']),
					'file_ext' => '.'.pathinfo($file_path,PATHINFO_EXTENSION),
					'chunk_id'  => $i,
					'hash_file' => md5_file($chunk['file']),
					'file_data' => '@'.$chunk['file'] // don't include the file in the signature
				);
				
				if(($number_of_chunks -1) == $i){
				$params['hash_file'] = md5_file($file_path);
				}
				
			  
				// Generate the OAuth signature
				$params = array_merge($params, array(
					'file_data' => '@'.$chunk['file'] // don't include the file in the signature
				));
				// Post the file
				$rsp = $this->actionCurl($endpoint, $params);
				$this->notify(null,null,null,null,null,'progress-bar',null,$chunkAmount,'Uploading Media');
				echo $rsp;
				sleep(5);
		}
	
	}
	}