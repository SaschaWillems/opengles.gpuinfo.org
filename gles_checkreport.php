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


	DB::connect();

	$description = $_GET["description"];
	
/*
	$sqlresult = mysql_query("select id from reports where description = '$description'");
	$sqlcount = mysql_num_rows($sqlresult);   
	$sqlrow = mysql_fetch_row($sqlresult);
	
	if ($sqlcount > 0) {
		header('HTTP/ 433 report_present '.$sqlrow[0].'');
		echo "Report for '$description' already present";
	} else {
		header('HTTP/ 433 report_new');
		echo "Report for '$description' is new";
	}
*/

	try {
		$stmnt = DB::$connection->prepare("SELECT id from reports where description = :description");
		$stmnt->execute(['description' => $description]);	
		$row = $stmnt->fetch(PDO::FETCH_NUM);
		if ($row) {
			header("HTTP/ 433 report_present $row[0]");
			echo "Report for '$description' already present";
		} else {
			header('HTTP/ 433 report_new');
			echo "Report for '$description' is new";
		}
	} catch (PDOException $e) {
		mail($mailto, "Error while checking for report duplicate", $e->getMessage());
		header('HTTP/1.0 500 server_error');
	}
	mail($mailto, "Report check GLES" , $description);
	DB::disconnect();

?>