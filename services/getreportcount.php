 <?php
 	/*
		*
		* OpenGL ES hardware capability database server implementation
		*
		* Copyright (C) 2013-2015 by Sascha Willems (www.saschawillems.de)
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
	include './../serverconfig/gles_config.php';	

	dbConnect();
	
	$sqlResult = mysql_query("select count(*) from reports") or die(mysql_error());
	$sqlCount = mysql_result($sqlResult, 0);
	$arr = array('count' => $sqlCount);	
	echo "jsoncallback(".json_encode($arr).")";			
	
	dbDisconnect();
?>     