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
 
	// Filter criteria
	$extension = mysql_real_escape_string($_GET['extension']);
	$extensionunsupported = mysql_real_escape_string($_GET['extensionunsupported']);
	$eglextension = mysql_real_escape_string($_GET['eglextension']);
	$eglextensionunsupported = mysql_real_escape_string($_GET['eglextensionunsupported']);
	$glesversion = mysql_real_escape_string($_GET['glesversion']);
	$shadinglanguageversion = mysql_real_escape_string($_GET['shadinglanguageversion']);
	$devicefeature = mysql_real_escape_string($_GET['devicefeature']);
	$androidversion = mysql_real_escape_string($_GET['androidversion']);
	$cpuarch = mysql_real_escape_string($_GET['cpuarch']);
	$submitter = mysql_real_escape_string($_GET['submitter']);
	$compressedtextureformat = mysql_real_escape_string($_GET['compressedtextureformat']);

	// Params
	$groupby = $_GET['groupby'];
	
    if ($extension != '')
        $header = "Listing reports supporting extension <strong>".strtoupper($extension)."</strong>";

    if ($extensionunsupported != '')
        $header = "Listing reports <font color=red>not</font> supporting extension <strong>".strtoupper($extensionunsupported)."</strong>";        

    if ($devicefeature != '')
        $header = "Listing reports supporting device feature <strong>".strtoupper($devicefeature)."</strong>";
    
    if ($compressedtextureformat != '')
        $header = "Listing reports supporting format <strong>".strtoupper($compressedtextureformat)."</strong>";
	
    if ($header == '') 
    {
        $sqlResult = mysql_query("SELECT count(*) FROM reports");
        $sqlCount = mysql_result($sqlResult, 0);
        $header = "Listing all reports ($sqlCount)";
    }   
                
	echo "<div class='header'>";
		echo "<h4 style='margin-left:10px;'>$header</h4>";
	echo "</div>";		

    function shorten($string, $length) 
    {
        if (strlen($string) >= $length)
        {
            return substr($string, 0, $length-10). " ... " . substr($string, -5);
        }
        else 
        {
            return $string;
        }
    }
	
?>

<center>
	<div class="reportdiv">

	<form method="get" action="gles_comparereports.php?" style="margin-bottom:0px;">

		<table id="reports" class="table table-striped table-bordered table-hover reporttable">
			<?php

		$fields = 'reports.id, devicename(reports.device), reports.os, reports.esversion_major, reports.esversion_minor, reports.shadinglanguageversion_major, reports.shadinglanguageversion_minor, date(reports.submissiondate), reports.gl_renderer';   

		$sqlquery = "";
		
		$caption = "";
		
		// List by extension
		if ($extension != '') {	
			$caption = "Devices supporting $extension";
			$sqlquery = "select $fields from reports join reports_extensions rext on rext.REPORTID = reports.id join extensions ext on ext.id = rext.EXTENSIONID where ext.name = '$extension' order by description";
		}
		
		// List by extension not supported
		if ($extensionunsupported != '') {	
			$caption = "Devices not supporting $extensionunsupported";
			$sqlquery = "select $fields from reports where
			id not in (select r.id from reports r join reports_extensions rext on rext.REPORTID = r.id 
			join extensions ext on ext.id = rext.EXTENSIONID 
			where ext.name = '$extensionunsupported' order by description)";
		}

		// List by egl extension
		if ($eglextension != '') {	
			$caption = "Devices supporting $eglextension";
			$sqlquery = "select $fields from reports join reports_eglextensions rext on rext.REPORTID = reports.id join egl_extensions ext on ext.id = rext.ID where ext.name = '$eglextension' order by description";
		}
		
		// List by egl extension not supported
		if ($eglextensionunsupported != '') {	
			$caption = "Devices not supporting $eglextensionunsupported";
			$sqlquery = "select $fields from reports where
			id not in (select r.id from reports r join reports_eglextensions rext on rext.REPORTID = r.id 
			join egl_extensions ext on ext.id = rext.ID  
			where ext.name = '$eglextensionunsupported' order by description)";
		}
		
		// List by compressed texture format
		if ($compressedtextureformat != '') {	
			$caption = "Devices supporting $compressedtextureformat";
			$sqlquery = "select $fields from reports join reports_compressedformats rcf on rcf.REPORTID = reports.id join compressedformats cf on cf.id = rcf.compressedformatid where cf.name = '$compressedtextureformat' order by description";
		}
		
		// List by ES version
		if ($glesversion != '') {	
			$caption = "Devices supporting OpenGL ES $glesversion";
			$sqlquery = "select $fields from reports where concat(cast(esversion_major as char(2)), '.', cast(esversion_minor as char(2))) = $glesversion";
		}

		// List by android version
		if ($androidversion != '') {	
			$caption = "Devices with Android $androidversion";   
			$sqlquery = "select $fields from reports where os = '$androidversion'";
		}	
		
		// List by CPU architecture
		if ($cpuarch != '') {	
			$caption = "Devices with CPU archtitecture $androidversion";   
			$sqlquery = "select $fields from reports where cpuarch = '$cpuarch'";
		}			
		
		// List by submitter
		if ($submitter != '') {	
			$caption = "Devices submitted by $submitter";   
			$sqlquery = "select $fields from reports where submitter = '$submitter'";
		}		
		
		// List by ES shading language version
		if ($shadinglanguageversion != '') {	
			$caption = "Devices supporting shading language version $shadinglanguageversion";   
			$sqlquery = "select $fields from reports where concat(cast(SHADINGLANGUAGEVERSION_MAJOR as char(2)), '.', cast(SHADINGLANGUAGEVERSION_MINOR as char(2))) = $shadinglanguageversion";
		}

		// Device feature
		if ($devicefeature != '') {	
			$caption = "Devices supporting feature $devicefeature";   
			$sqlquery = "select $fields from reports join reports_devicefeatures rdev on rdev.REPORTID = reports.id join devicefeatures dev on dev.id = rdev.devicefeatureid where dev.DEVICEFEATURE = '$devicefeature' order by description";
		}
		
		// List all reports
		
		if ($sqlquery === '') {
			$sqlquery = "select $fields from reports $like order by submissiondate desc";
			$sqlresult = mysql_query("$sqlquery");	
			$sqlcount = mysql_num_rows($sqlresult);   
			$caption = "Listing $sqlcount devices";   
		}
		
		$sqlresult = mysql_query($sqlquery);	
		$sqlcount = mysql_num_rows($sqlresult);   
		$caption = "$sqlcount ".$caption;
			
		echo "<thead><tr>";
		echo "	<td class='caption'>Name</td>";
		echo "	<td class='caption'>GLES</td>";
		echo "	<td class='caption'>SL</td>";
		echo "	<td class='caption'>Renderer</td>";
		echo "	<td class='caption'>Android</td>";
		echo "	<td class='caption'>Date</td>";
		echo "	<td class='caption' align='center'><input type='submit' value='compare'></td>";   		
		echo "</tr></thead><tbody>";
		
		while($row = mysql_fetch_row($sqlresult)) 
		{	
			echo "<tr>";
			echo "<td class='value'><nobr><a href='gles_generatereport.php?reportID=".$row[0]."'>".shorten($row[1], 35)."</a></nobr></td>";
			echo "<td class='valuezeroleft'>".$row[3].".".$row[4]."</td>";
			if ($row[5] == 0) 
			{
				echo "<td class='valuezeroleft'>-</td>";
			} else {
				echo "<td class='valuezeroleft'>".$row[5].".".$row[6]."</td>";
			}
			echo "<td class='valuezeroleft'>".shorten($row[8], 20)."</td>";
			echo "<td class='valuezeroleft'>".$row[2]."</td>";
			echo "<td class='valuezeroleft'><nobr>".$row[7]."</nobr></td>";
			echo "<td class='valuezeroleft'><center><input type='checkbox' name='$row[0]' value='1'></center></td>";
			echo "</tr>"; 
		}	  
		
	echo "</tbody></table>";         	
	echo "</form>";
	echo "</div>";
	include("./gles_footer.inc");	
	
	dbDisconnect();
    ?>     

	<script>
		$(document).ready(function() {
			$('#reports').DataTable({
				"order": [[ 6, "desc" ]],
				"deferRender": true,
				"pageLength" : 50,
				"stateSave": false,
				"searchHighlight": true,
				"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
				"orderCellsTop": true,

				initComplete: function () {
					var api = this.api();

					api.columns().indexes().flatten().each( function ( i ) {
						if ((i>0) && (i<5)) {						
							var column = api.column( i );
							var select = $('<br/><select onclick="stopPropagation(event);"><option value=""></option></select>')
							.appendTo( $(column.header()) )
							.on( 'change', function () {
								var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
								);

								column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
							} );	

							column.data().unique().sort().each( function ( d, j ) {
								select.append( '<option value="'+d+'">'+d+'</option>' )
							} );
						};
					} );
				}

			});
		} );
		
	  function stopPropagation(evt) {
			if (evt.stopPropagation !== undefined) {
				evt.stopPropagation();
			} else {
				evt.cancelBubble = true;
			}
		}		
	</script>	
	
</body>
</html>