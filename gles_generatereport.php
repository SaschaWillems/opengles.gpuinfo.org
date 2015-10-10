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
 
	include './gles_htmlheader.inc';
	include './serverconfig/gles_config.php';	
	
	dbConnect();

	function generate_table($sql) {

		$sqlresult = mysql_query($sql);			
		$column    = array();
		$captions  = array();
		while($row = mysql_fetch_row($sqlresult)) {		
			$colindex = 0;
			$reportdata = array();		
			foreach ($row as $data) {			
				$reportdata[] = $data;	  
				$captions[]   = mysql_field_name($sqlresult, $colindex);			
				if (mysql_field_name($sqlresult, $colindex) == 'device') {
					$reportdevices[] = $data;
				}
				$colindex++;
			} 
			$column[] = $reportdata; 
		}
		
		// Generate table from array
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($column[0]); $i < $arrsize; ++$i) { 	  
			echo "<tr>\n";
			echo "<td class='firstcolumn'>".$captions[$i]."</td>\n";			
			for ($j = 0, $subarrsize = sizeof($column); $j < $subarrsize; ++$j) {	 
				if ($captions[$i] == "submitter") {
					echo "<td class='valuezeroleftdark'><a href='.\gles_listreports.php?submitter=".$column[$j][$i]."'>".$column[$j][$i]."</a></td>";
				} else {
					echo "<td class='valuezeroleftdark'>".$column[$j][$i]."</td>";
				}
			} 
			echo "</tr>\n";
			$rowindex++;
		}   
			
	}
	
	function generate_extension_table($reportids, $sql, $checksql) {
	

		$sqlresult = mysql_query($sql); 
		$extcaption = array(); 
				

		while($row = mysql_fetch_row($sqlresult)) {	
			foreach ($row as $data) {
				$extcaption[] = $data;	  
			}
		}

		$extarray   = array(); 
		foreach ($reportids as $repid) {
			$sqlresult = mysql_query($checksql."= $repid"); 
			$subarray = array();
			while($row = mysql_fetch_row($sqlresult)) {	
					foreach ($row as $data) {
					$subarray[] = $data;	  
				}
			}
			$extarray[] = $subarray; 
		}
		
		// Generate table
		$arrcount = count($extcaption);
		if ($arrcount > 0) {
			$colspan = count($reportids) + 1;	
			$rowindex = 0;
			foreach ($extcaption as $extension){
				echo "<tr><td class='fieldcaption'>$extension</td>\n";		 
				$index = 0;
				foreach ($reportids as $repid) {
					if (in_array($extension, $extarray[$index])) { 
						echo "<td class='value' style='margin-left:10px;'><img src='icon_check.png'/ width=16px></td>";
					} else {
						echo "<td class='value'></td>";
					}	
					$index++;
				}  
				$rowindex++;
			echo "</tr>\n"; 	
			}
		} else {
			echo "<tr><td class='fieldcaption' colspan=$colspan>None</td></tr>\n";		 
		}
	}
	
	function generate_caps_table($sql, $esversion) {	
	

		$sqlresult = mysql_query($sql);	
		$column    = array();
		$captions  = array();
		while($row = mysql_fetch_row($sqlresult)) {		
			$colindex = 0;
			$reportdata = array();		
			foreach ($row as $data) {			
				$reportdata[] = $data;	  
				$captions[]   = mysql_field_name($sqlresult, $colindex);			
				if (mysql_field_name($sqlresult, $colindex) == 'device') {
					$reportdevices[] = $data;
				}
				$colindex++;
			} 
			$column[] = $reportdata; 
		}
								
		// Generate table from array
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($column[0]); $i < $arrsize; ++$i) { 	  
			if ($captions[$i] != 'REPORTID') {
				echo "<tr>\n";
				echo "<td class='firstcolumn'>".$captions[$i]."</td>\n";
				for ($j = 0, $subarrsize = sizeof($column); $j < $subarrsize; ++$j) {	 
					echo "<td class='valuezeroleftdark'>".number_format($column[$j][$i], 0, '.', ',')."</td>";
				} 
				echo "</tr>\n";
			}
			$rowindex++;
		}   
		
	}	
	
	function generate_list_table($sql, $caption, $linkprefix) {
	
		$sqlresult = mysql_query($sql);   
		$sqlcount = mysql_num_rows($sqlresult);
		
//		echo "<TR> <TD class='reporttableheader' colspan=2><b>$caption ($sqlcount)</b> </TD></TR>\n";   			
		
		$rowindex = 0;
		while($row = mysql_fetch_row($sqlresult)) {	
			foreach ($row as $data) {
				echo "<tr>\n";
				$link = $data;
				if ($linkprefix != '') {
					$link = "<a href='$linkprefix$data'>$data</a>";
				}
				echo "<td class='firstcolumn' colspan=2>$link</td>\n";        
				echo "</tr>";
				$rowindex++;
			}
		}	  	
	}
    
    function getCount($sql)
    {
        $sqlresult = mysql_query($sql) or die(mysql_error());
        return mysql_result($sqlresult, 0);    
    }

    $reportID = mysql_real_escape_string($_GET['reportID']); 
    $sqlresult = mysql_query("SELECT description, devicename(device) as device, GL_VERSION, esversion_major, id FROM reports WHERE ID = $reportID");
    $row = mysql_fetch_array($sqlresult);
    $sqlcount = mysql_num_rows($sqlresult);   
    $esversion = $row['esversion_major'];    
    
	$sensorCount = getCount("select count(*) from reports_sensors where ReportID = $reportID");       
    $extCount = getCount("select count(*) from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid = $reportID");
	$extEglCount = getCount("select count(*) from reports_eglextensions where reportid = $reportID");
    $featureCount = getCount("select count(*) from reports_devicefeatures Tlookup join devicefeatures Tjoin on Tlookup.DEVICEFEATUREID = Tjoin.id where Tlookup.reportid = $reportID");
	$compressedformatCount = getCount("select count(*) from reports_compressedformats where reportid = $reportID");
	   
    echo "<center>";   
    
	// Header =====================================================================================
    echo "<div class='header'>";
	echo "<h4 style='margin-left:10px;'>Report for '".$row['device']." (".$row['GL_VERSION'].")'</h4>";
	echo "</div>";
				
	// Tabs =======================================================================================
	echo "<div id='tabs' style='font-size:12px;'>";
	echo "<ul class='nav nav-tabs'>";
	echo "	<li><a data-toggle='tab' href='#tabs-implementation'>Implementation</a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-gl-extensions'>GL Extensions <span class='badge'>$extCount</span></a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-egl-extensions'>EGL Extensions <span class='badge'>$extEglCount</span></a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-compressedformats'>Compr. formats <span class='badge'>$compressedformatCount</span></a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-shaderformats'>Shader formats</a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-sensors'>Sensors <span class='badge'>$sensorCount</span></a></li>";
	echo "	<li><a data-toggle='tab' href='#tabs-features'>Features <span class='badge'>$featureCount</span></a></li>";
	echo "</ul>";    
    
    echo "<div id='content'>";	
	
	// Implementation
	echo "<div id='tabs-implementation' class='reportdiv'>";
	echo "<table id='implementation' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Capability</td><td>Value</td></tr></thead><tbody>";   
           
	// Device info
	generate_table("SELECT devicename(device) as device, os, screenwidth, screenheight, cpucores, cpuspeed, cpuarch, submissiondate, submitter FROM reports WHERE ID = $reportID");
          
    // ES renderer
    echo "<tr><td><b>OpenGL ES renderer</b></td><td></td></tr>";   
    generate_table("SELECT GL_VENDOR, GL_RENDERER, GL_VERSION, GL_SHADING_LANGUAGE_VERSION FROM reports WHERE ID = $reportID");
	
    // EGL implementation
    echo "<tr><td><b>EGL implementation</b></td><td></td></tr>";   
    generate_table("SELECT EGL_VENDOR, EGL_VERSION,
		(select GROUP_CONCAT(name) from reports_eglclientapis Tlookup join egl_clientapis Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID) as 'Client APIs'
		FROM reports WHERE ID = $reportID");
        
	// ES 2.0 capabilities
    echo "<tr><td><b>OpenGL ES 2.0 capabilities</b></td><td></td></tr>";   
	if ($esversion >= 2) 
	{
		generate_caps_table("SELECT * from reports_es20caps where ReportID = $reportID", 2);
	} 
	else 
	{
	   echo "<tr><td class='firstcolumn' style='color:#FF0000;'>not supported</td><td></td></tr>";
	}

	// ES 3.0 capabilities
    echo "<tr><td><b>OpenGL ES 3.0 capabilities</b></td><td></td></tr>";   
	if ($esversion >= 3) 
	{
		generate_caps_table("SELECT * from reports_es20caps where ReportID = $reportID", 3);
	} 
	else 
	{
	   echo "<tr><td class='firstcolumn' style='color:#FF0000;'>not supported</td><td></td></tr>";
	}           
    echo "</tbody></table></div>";

	// GL Extensions
	echo "<div id='tabs-gl-extensions' class='reportdiv'>";
	echo "<table id='extensions' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Extension</td></tr></thead><tbody>";
	generate_list_table("select name from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid = $reportID", 'OpenGL ES Extensions', './gles_listreports.php?extension=');	
	echo "</tbody></table></div>";   
	
	// EGL Extensions
	echo "<div id='tabs-egl-extensions' class='reportdiv'>";
	echo "<table id='eglextensions' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Extension</td></tr></thead><tbody>";
    generate_list_table(
		"select name from reports_eglextensions Tlookup join egl_extensions Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID", 
		'EGL Extensions', 
		'./gles_listreports.php?eglextension=');
	echo "</tbody></table></div>";   	

	// Commpressed formats
	echo "<div id='tabs-compressedformats' class='reportdiv'>";
	echo "<table id='compressedformats' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Format</td></tr></thead><tbody>";
    generate_list_table(
		"select name from reports_compressedformats rcf join compressedformats cf on rcf.compressedformatid = cf.id where rcf.reportid = $reportID and cf.name != '0x0'", 
		'Compressed texture formats', 
		'./gles_listreports.php?compressedtextureformat=');
	echo "</tbody></table></div>";   
	
	// Shader formats
	echo "<div id='tabs-shaderformats' class='reportdiv'>";
    // ES binary shader formats
	echo "<h3>Binary shader formats</h3>";
	echo "<table id='shaderformats' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Format</td></tr></thead><tbody>";
    generate_list_table(
		"select name from reports_binaryshaderformats rbsf join binaryshaderformats sf on rbsf.binaryshaderformatid = sf.id where rbsf.reportid = $reportID", 
		'Binary shader formats', '');
	echo "</tbody></table>";   	
    // ES binary program formats
	echo "<h3>Binary program formats</h3>";
	echo "<table id='programformats' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Format</td></tr></thead><tbody>";
    generate_list_table(
		"select name from reports_binaryprogramformats Tlookup join binaryprogramformats Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID", 
		'Binary program formats', '');
	echo "</tbody></table>";   	
	echo "</tbody></table></div>";   	
		
	// Sensors ===============================================================================================
	echo "<div id='tabs-sensors' class='reportdiv'>";
	echo "<table id='sensors' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr>";
    echo "  <td>Name</td>";
    echo "  <td>Max.Range</td>";
    echo "  <td>Resolution</td>";
    echo "</tr></thead><tbody>";
    
	$str = "select name,maxrange,resolution from reports_sensors where ReportID = $reportID";  
	$sqlresult = mysql_query($str);  
	$extarray = array();
	while($row = mysql_fetch_row($sqlresult)) 
    {	
        echo "<tr>";
        echo "<td class='valuezeroleft'>".$row[0]."</td>";
        echo "<td class='valuezeroleft'>".$row[1]."</td>";
        echo "<td class='valuezeroleft'>".$row[2]."</td>";
        echo "</tr>";
	}
    
	echo "</tbody></table></div>";          
    echo "</div>";
    
	// Features ==============================================================================================
	echo "<div id='tabs-features' class='reportdiv'>";
	echo "<table id='features' class='table table-striped table-bordered table-hover reporttable'>";
	echo "<thead><tr><td>Extension</td></tr></thead><tbody>";
	generate_list_table("select devicefeature from reports_devicefeatures Tlookup join devicefeatures Tjoin on Tlookup.DEVICEFEATUREID = Tjoin.id where Tlookup.reportid = $reportID", '', '');	
	echo "</tbody></table></div>";   
    

    include("./gles_footer.inc");
    
	dbDisconnect();    
    ?>     

	<script>
    	$(document).ready(function() 
        {
            var tableNames = [ "#implementation", "#extensions", '#eglextensions', '#compressedformats', '#sensors', '#features', '#shaderformats', '#programformats' ];
	        for (var i=0; i < tableNames.length; i++) 
            {           
                $(tableNames[i]).DataTable({
					"paging" : false,
                    "order": [], 
                    "searchHighlight": true
                });
            }
		} );	
	</script>    
    
</center>	   
</body>
</html>