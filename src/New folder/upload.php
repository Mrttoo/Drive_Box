<?php
/*
 * Copyright 2011 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once __DIR__ . '/../vendor/autoload.php';
include_once "templates/base.php";

echo pageHeader("File Upload - Uploading a large file");

/*************************************************
 * Ensure you've downloaded your oauth credentials
 ************************************************/
if (!$oauth_credentials = getOAuthCredentialsFile()) {
  echo missingOAuth2CredentialsWarning();
  return;
}

/************************************************
 * The redirect URI is to the current page, e.g:
 * http://localhost:8080/large-file-upload.php
 ************************************************/
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$client = new Google_Client();
$client->setAuthConfig($oauth_credentials);
$client->setRedirectUri($redirect_uri);
$client->addScope("https://www.googleapis.com/auth/drive");
$service = new Google_Service_Drive($client);

// add "?logout" to the URL to remove a token from the session
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['upload_token']);
}

/************************************************
 * If we have a code back from the OAuth 2.0 flow,
 * we need to exchange that with the
 * Google_Client::fetchAccessTokenWithAuthCode()
 * function. We store the resultant access token
 * bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token);

  // store in the session also
  $_SESSION['upload_token'] = $token;

  // redirect back to the example
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['upload_token'])) {
  $client->setAccessToken($_SESSION['upload_token']);
  if ($client->isAccessTokenExpired()) {
    unset($_SESSION['upload_token']);
  }
} else {
  $authUrl = $client->createAuthUrl();
}

/************************************************
 * If we're signed in then lets try to upload our
 * file.
 ************************************************/

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $client->getAccessToken()) {

  $name = $_FILES['file']['name'];
  $filename = ($_FILES['file']['tmp_name']);
  $createdFile = insertFile($service, $name, $parentID = null, $filename);
  
}

function insertFile($service, $name, $parentId, $filename) {
  $file = new Google_Service_Drive_DriveFile();
  $file->setName($name);
 
  // Set the parent folder.
  if ($parentId != null) {
    $parent = new Google_Service_Drive_ParentReference();
    $parent->setId($parentId);
    $file->setParents(array($parent));
  }

  try {
    $data = file_get_contents($filename);

    $createdFile = $service->files->create($file, array(
      'data' => $data,
    ));

    return $createdFile;
  } catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
  }
}

?>

<div class="box">
<?php if (isset($authUrl)): ?>
  <div class="request">
    <a class='login' href='<?= $authUrl ?>'>Connect Me!</a>
  </div>
<?php elseif($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
  <div class="shortened">
    <p>Your call was successful! Check your drive for this file:</p>
    <p><a href="https://drive.google.com/open?id=<?= $createdFile->getId() ?>" target="_blank"><?= $createdFile->getName() ?></a></p>
  </div>
<?php else: ?>
  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" value="Select file" />
    <input type="submit" value="Upload file" />
  </form>
<?php endif ?>
</div>

