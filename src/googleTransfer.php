<?php


class GoogleTransfer
{
	private $service;

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
	 * [insertFile description]
	 * @param  Google_Service_Drive $service   
	 * @param  string $name      
	 * @param  int $parentId  
	 * @param  string $fileStream 
	 */
	function insertFile($service, $name, $parentId, $fileStream) {
		$file = new Google_Service_Drive_DriveFile();
		$file->setName($name);

		  // Set the parent folder.
		if ($parentId != null) {
			$parent = new Google_Service_Drive_ParentReference();
			$parent->setId($parentId);
			$file->setParents(array($parent));
		}

		try {
			$data = file_get_contents($fileStream);
			$createdFile = $service->files->create($file, array(
				'data' => $data,
				));

			return $createdFile;
		} catch (Exception $e) {
			print "An error occurred: " . $e->getMessage();
		}
	}

	/**
	 * Upload files into google drive
	 */
	function uploadFiles()
	{
		$files = $this->getFiles();
			foreach ($files as $name => $file) {
				foreach ($file as $key => $value) {
					echo $value;
					echo $name;
					$this->insertFile($this->service, $name,  $parentID = null, $value);
				}
			}
	}

	function __construct($client)
	{
		$this->service = new Google_Service_Drive($client);

		if(isset($_POST['submit'])){
			$this->uploadFiles();
		}
	}
}

