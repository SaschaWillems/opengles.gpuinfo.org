<?php
	/* 		
		*
		* OpenGL ES hardware capability database server implementation
		*
		* Upload and convert a glESCapsViewer report and insert into database if not present
		*	
		* Copyright (C) 2011-2018 by Sascha Willems (www.saschawillems.de)
		*	
		* This code is free software, you can redistribute it and/or
		* modify it under the terms of the GNU Affero General Public
		* License version 3 as published by the Free Software Foundation.
		*	
		* Please review the following information to ensure the GNU Lesser
		* General Public License version 3 requirements will be met:
		* http://www.gnu.org/licenses/agpl-3.0.de.html
		*	
		* The code is distributed WITHOUT ANY WARRANTY; without even the
		* implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
		* PURPOSE.  See the GNU AGPL 3.0 for more details.		
		*
	*/

	include 'dbconfig.php';

	// Check for valid file
	$path='./uploads/';

	// Reports are pretty small, so limit file size for upload (128 KByte will be more than enough)
	$MAX_FILESIZE = 128 * 1024;

	$file = $_FILES['data']['name'];
	
	$IP = $_SERVER["REMOTE_ADDR"];	

	// Skip if file exceeds size of 128 KBytes
	if ($_FILES['data']['size'] > $MAX_FILESIZE) 
	{
		echo "File exceeds size limitation of 128 KByte!";    
		header('HTTP/ 433 File too big!');
		exit();  
	}

	// Skip if not XML
	$ext = pathinfo($_FILES['data']['name'], PATHINFO_EXTENSION); 
	if ($ext != 'xml')
	{
		echo "Report '$file' is not a valid XML file!";    
		header('HTTP/ 433 Not a valid XML file!');
		exit();  
	} 

	$msg = $_FILES['data']['name'];
	
	// Upload files
	if (!move_uploaded_file($_FILES['data']['tmp_name'], $path.$_FILES['data']['name'])) {
		mail($mailto, 'Error glESCapsViewer report upload', $path.$_FILES['data']['name']); 			
		die('');
	}; 

	$xml = file_get_contents($path.$_FILES['data']['name']);
	
	DB::connect();
	
	$xmlstring = str_replace("'", "\\'", $xml);
	
	// Check if report already exists
	try {
		$stmnt = DB::$connection->prepare("SELECT check_glesreport('$xmlstring');");
		$stmnt->execute();	
		$row = $stmnt->fetch(PDO::FETCH_NUM);
		$res = explode("|", $row[0]);
		if ($res[0] == "duplicate") {
			mail($mailto, 'Duplicate report for '.$res[1], "Duplicate report submitted :\nDevice = $res[1]\nIP = $IP"); 	
			header('HTTP/1.0 200 res_duplicate');
			DB::disconnect();
			die('');
		}			
	} catch (PDOException $e) {
		mail($mailto, "Error while uploading report (check if present)", $e->getMessage());
		header('HTTP/1.0 500 server_error');
		DB::disconnect();
		die('');
	}
	
	try {
		$stmnt = DB::$connection->prepare("CALL import_glesreport('$xmlstring');");
		$stmnt->execute();	
	} catch (PDOException $e) {
		mail($mailto, "Error while uploading report (import)", $e->getMessage()."\nXML".$xmlstring);
		header('HTTP/1.0 500 server_error');
		DB::disconnect();
		die('');
	}

	$stmnt = DB::$connection->prepare("SELECT Max(ID) as ReportID FROM reports");
	$stmnt->execute();
	$row = $stmnt->fetch(PDO::FETCH_ASSOC);
	$reportID = $row["ReportID"];	

	header('HTTP/1.0 200 res_uploaded');	

	$stmnt = DB::$connection->prepare("SELECT device, GL_RENDERER, GL_VERSION, os, cpuarch, submitter from reports where id = :reportid");
	$stmnt->execute(["reportid" => $reportID]);
	$reportDetails = $stmnt->fetch(PDO::FETCH_NUM);

	$msgtitle = "New OpenGL ES report for ".$reportDetails[0]." (".$reportDetails[1].")";

	$msg = "New OpenGL ES hardware report uploaded to the database\n\n";
	$msg .= "Link : http://opengles.gpuinfo.org/gles_generatereport.php?reportID=$reportID \n";	
	$msg .= "Devicename = ".$reportDetails[0]."\n";
	$msg .= "Renderer = ".$reportDetails[1]."\n";
	$msg .= "Version = ".$reportDetails[2]."\n";
	$msg .= "Android = ".$reportDetails[3]."\n";
	$msg .= "Architecture = ".$reportDetails[4]."\n";
	$msg .= "Submitter = ".$reportDetails[5]."\n";
	
	mail($mailto, $msgtitle, $msg); 	

	DB::disconnect();	 
?>