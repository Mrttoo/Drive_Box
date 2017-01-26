<?php

include 'authorize.php';
include 'FileTransfer.php';
include 'fileupload/dbxupload.php';

if(!isset($_SESSION)){session_start();}


if(!isset($_SESSION['auth'])){
$_SESSION['auth'] = new Authorize();
$_SESSION['auth']->obtainWebAuth(__DIR__ . '/../dropbox-cred.json', "FileTransfer/2.0", 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

echo $_SESSION['auth']->getAccessToken();

}
$_SESSION['auth']->obtainAccessToken();
print("<pre>".print_r($_SESSION,true)."</pre>");
if(isset($_REQUEST['upload'])){
$upload = new DbxUpload($_SESSION['auth']->getAccessToken(), "FileTransfer/2.0");
$upload->getFiles();
$upload->UploadFiles();
}

?>

<?php if($_SESSION['auth']->getAccessToken()): ?>

<form action="dbxtest.php?upload" enctype="multipart/form-data" method="post">

	<div>
		<label for='upload'>Add Attachments:</label>
		<input id='upload' name="upload[]" type="file" multiple="multiple" />
	</div>

	<p><input type="submit" name="submit" value="Submit"></p>

</form>

<?php else: ?>
	<a href="<?php echo $_SESSION['auth']->getAuthorizeUrl() ?>">Connect</a>
<?php endif; ?>


