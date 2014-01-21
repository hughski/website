<?php

/*
 * Copyright (C) 2012 Richard Hughes <richard@hughsie.com>
 *
 * Licensed under the GNU General Public License Version 2
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

$uploaddir = '/home/hughsiec/public_html/hughski/uploads/';

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
	if (strcmp(substr($data,0,4), "CCMX") != 0) {
		header('HTTP/1.0 403 Forbidden');
		echo 'Not an CCMX file';
	} else {
		// copy the profile and return the URL
		$fn = basename($_FILES['upload']['name']);
		$destination = $uploaddir . $fn;
		$handle = fopen($destination, "w");
		fwrite($handle, $data);
		fclose($handle);

		// send email
		$to      = 'info@hughski.com';
		$subject = 'CCMX added';
		$message = 'http://www.hughski.com/uploads/' . $fn;
		$headers = 'From: richard@hughsie.com' . "\r\n" .
		    'Reply-To: richard@hughsie.com' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $message, $headers);

		// return the created path in the location
		header('HTTP/1.0 201 Created');
		header('Location: http://www.hughski.com/uploads/' . $fn);
	}
}

?>
