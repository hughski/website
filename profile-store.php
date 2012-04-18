<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

$uploaddir = '/home/hughsiec/public_html/uploads/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

// perform some checks before opening the file
$size = $_FILES['upload']['size'];
if ($size > 10240) {
	header('HTTP/1.0 403 Forbidden');
	echo 'File size too large';
} elseif ($size < 128) {
	header('HTTP/1.0 403 Forbidden');
	echo 'File size too small';
} else {
	// open the file and read the contents
	$data = file_get_contents($_FILES['upload']['tmp_name']);

	// check the file is really an icc profile
	if (strcmp(substr($data,36,4), "acsp") != 0) {
		header('HTTP/1.0 403 Forbidden');
		echo 'Not an ICC profile';
	} else {
		// copy the profile and return the URL
		$sha1 = sha1($data);
		$destination = '/home/hughsiec/public_html/uploads/' . $sha1 . '.icc';
		$handle = fopen($destination, "w");
		fwrite($handle, $data);
		fclose($handle);

		// return the created path in the location
		header('HTTP/1.0 201 Created');
		header('Location: http://www.hughski.com/uploads/' . $sha1 . '.icc'); 
	}
}

?>
