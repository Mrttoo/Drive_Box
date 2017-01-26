<?php


//$db= mysqli_connect('127.0.0.1','root','','api');

include 'googleauthorize.php';
include 'googleTransfer.php';
include 'fileupload/googleupload.php';



session_start();


$auth = new GoogleAuthorize(__DIR__ . '/../oauth-credentials.json', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], "https://www.googleapis.com/auth/drive");
$auth->getToken();
if(isset($_REQUEST['upload']))
{
	print("<pre>".print_r($_SESSION,true)."</pre>");
	$auth->setToken($_SESSION['access_token']['google_drive']);
	$upload = new googleUpload($auth->getGoogleClient());
	$upload->getFiles();
	$upload->uploadFiles();
}



/*if($refreshToken = $auth->getRefreshToken()){
	echo $refreshToken;
	$sql = "INSERT INTO token (token) VALUES ('$refreshToken')";
	echo $sql;
	var_dump(mysqli_query($db, $sql));
}
else{
$sql = "SELECT * FROM token";
$result = mysqli_query($db, $sql);
$a = mysqli_fetch_assoc($result);
//echo $a['token'];
}
//save token into DB, if it's saved then we can connect user anytime

//load token from DB
$auth->getTokenWithRefreshToken($a['token']);
var_dump($_SESSION['access_token']);
*/



//Auth
//	get refresh token
//	getAccess token


//FileTransfer
//	create Google_Client
//	Create service
//	GetFile
//	InsertFile

?>

<?php if($auth->isLoggedIn()): ?>

<form action="googletest.php?upload" enctype="multipart/form-data" method="post">

	<div>
		<label for='upload'>Add Attachments:</label>
		<input id='upload' name="upload[]" type="file" multiple="multiple" />
	</div>

	<p><input type="submit" name="submit" value="Submit"></p>

</form>

<?php else: ?>
	<a href="<?php echo $auth->getAuthorizeUrl() ?>">Connect</a>
<?php endif; ?>




