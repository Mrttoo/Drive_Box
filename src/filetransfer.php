<?php

use \Dropbox as dbx;

class FileTransfer
{

	private $client;
	
	/**
	 * Get files from form into array 
	 * @return array 
	 */
	function getFiles(){
		if(isset($_POST['submit'])){
			if(count($_FILES['upload']['name']) > 0){
		       	//Loop through each file
				for($i=0; $i<count($_FILES['upload']['name']); $i++) {
		          //Get the temp file path
					$tmpFilePath = $_FILES['upload']['tmp_name'][$i];

		            //Make sure we have a filepath
					if($tmpFilePath != ""){

		                //save the filename
						$shortname = $_FILES['upload']['name'][$i];
						$files[$shortname][] = $tmpFilePath;
					}
				}
				return $files;
			}
		}
	}

	/**
	 * Upload files into Dropbox
	 */
	function uploadFiles()
	{
		$files = $this->getFiles();	
		foreach ($files as $name => $file) {
				foreach ($file as $key => $value) {
					echo $value;
					echo $name;
					echo $key;
					$fileStream = fopen($value, 'rb');
					$this->client->uploadFile("/".$name,dbx\WriteMode::add(), $fileStream);
				}
			}
	}

	

	function __construct($accessToken = null, $clientId = null)
	{
		
		//$this->client = new dbx\Client($accessToken, $clientId);
		if(isset($_POST['submit'])){
			$this->uploadFiles();
		}	
	}
}

?>


