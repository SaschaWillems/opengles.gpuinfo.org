 <?php
	/*
		*
		* OpenGL ES hardware capability database server implementation
		*
		* Copyright (C) 2013-2018 by Sascha Willems (www.saschawillems.de)
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
 
	include 'header.html';
	include 'dbconfig.php';	
	
	DB::connect();

	function generate_table($sql) {
		$stmnt = DB::$connection->prepare($sql);
		$stmnt->execute();
		$column = array();
		$captions = array();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			$colindex = 0;
			$reportdata = array();		
			foreach ($row as $data) {			
				$reportdata[] = $data;	  
				$meta = $stmnt->getColumnMeta($colindex);
				$captions[] = $meta["name"];
				$colindex++;
			} 
			$column[] = $reportdata;
		}
		
		// Generate table from array
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($column[0]); $i < $arrsize; ++$i) { 	  
			echo "<tr>\n";
			echo "<td class='subkey'>".$captions[$i]."</td>\n";			
			for ($j = 0, $subarrsize = sizeof($column); $j < $subarrsize; ++$j) {	 
				if ($captions[$i] == "Submitted by") {
					echo "<td><a href='.\listreports.php?submitter=".$column[$j][$i]."'>".$column[$j][$i]."</a></td>";
				} else {
					echo "<td>".$column[$j][$i]."</td>";
				}
			} 
			echo "</tr>\n";
			$rowindex++;
		}   
			
	}
			
	function generate_caps_table($sql, $esversion) {		
		$column    = array();
		$captions  = array();
		$stmnt = DB::$connection->prepare($sql);
		$stmnt->execute();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			$colindex = 0;
			$reportdata = array();		
			foreach ($row as $data) {			
				$reportdata[] = $data;	  
				$meta = $stmnt->getColumnMeta($colindex);
				$captions[] = $meta["name"];
				$colindex++;
			} 
			$column[] = $reportdata; 
		}
								
		// Generate table from array
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($column[0]); $i < $arrsize; ++$i) { 	  
			if ($captions[$i] != 'REPORTID') {
				echo "<tr>\n";
				echo "<td class='subkey'>".$captions[$i]."</td>\n";
				for ($j = 0, $subarrsize = sizeof($column); $j < $subarrsize; ++$j) {	 
					echo "<td class='valuezeroleftdark'>".number_format($column[$j][$i], 0, '.', ',')."</td>";
				} 
				echo "</tr>\n";
			}
			$rowindex++;
		}   
		
	}	
	
	function generate_list_table($sql, $caption, $linkprefix) {	
		$stmnt = DB::$connection->prepare($sql);
		$stmnt->execute();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			foreach ($row as $data) {
				echo "<tr>\n";
				$link = $data;
				if ($linkprefix != '') {
					$link = "<a href='$linkprefix$data'>$data</a>";
				}
				echo "<td class='firstcolumn' colspan=2>$link</td>\n";        
				echo "</tr>";
			}
		}	  	
	}
    
	$reportID = (int)($_GET['id']); 
	$stmnt = DB::$connection->prepare("SELECT description, devicename(device) as device, GL_VERSION, esversion_major, esversion_minor, reportversion, id FROM reports WHERE ID = :reportid");
	$stmnt->execute(["reportid" => $reportID]);	
	if ($stmnt->rowCount() == 0) {
		echo "<center>";
		?>
			<div class="alert alert-danger error">
			<strong>This is not the <strike>droid</strike> report you are looking for!</strong><br><br>
			Could not find report with ID <?php echo $reportID ?> in database.<br>
			It may have been removed due to faulty data.
			</div>				
		<?php
		include "footer.html";
		echo "</center>";
		DB::disconnect();
		die();			
	}	

	$row = $stmnt->fetch(PDO::FETCH_ASSOC);
	$esversion_major = $row['esversion_major'];    
	$esversion_minor =  $row['esversion_minor'];
	$reportversion = $row['reportversion'];
    
	$sensorCount = DB::getCount("SELECT count(*) from reports_sensors where ReportID = :reportid", ["reportid" => $reportID]);       
    $extCount = DB::getCount("SELECT count(*) from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid = :reportid", ["reportid" => $reportID]);
	$extEglCount = DB::getCount("SELECT count(*) from reports_eglextensions where reportid = :reportid", ["reportid" => $reportID]);
    $featureCount = DB::getCount("SELECT count(*) from reports_devicefeatures Tlookup join devicefeatures Tjoin on Tlookup.DEVICEFEATUREID = Tjoin.id where Tlookup.reportid = :reportid", ["reportid" => $reportID]);
	$compressedformatCount = DB::getCount("SELECT count(*) from reports_compressedformats where reportid = :reportid", ["reportid" => $reportID]);
	   
    echo "<center>";   
    
	// Header =====================================================================================
    echo "<div class='header'>";
	echo "<h4 style='margin-left:10px;'>Report for '".$row['device']." (".$row['GL_VERSION'].")'</h4>";
	echo "</div>";
				
?>
	<!-- Navigation -->
	<div>
		<ul class='nav nav-tabs'>
			<li class='active'><a data-toggle='tab' href='#tabs-implementation'>Implementation</a></li>
			<li><a data-toggle='tab' href='#tabs-gl-extensions'>GL Extensions <span class='badge'><?php echo $extCount ?></span></a></li>
			<li><a data-toggle='tab' href='#tabs-egl-extensions'>EGL Extensions <span class='badge'><?php echo $extEglCount ?></span></a></li>
			<li><a data-toggle='tab' href='#tabs-compressedformats'>Compr. formats <span class='badge'><?php echo $compressedformatCount ?></span></a></li>
			<li><a data-toggle='tab' href='#tabs-shaderformats'>Shader formats</a></li>
			<li><a data-toggle='tab' href='#tabs-sensors'>Sensors <span class='badge'><?php echo $sensorCount ?></span></a></li>
			<li><a data-toggle='tab' href='#tabs-features'>Features <span class='badge'><?php echo $featureCount ?></span></a></li>
		</ul>
	</div>

	<div class='tablediv tab-content' style='width:75%;'>

		<!-- Implementation -->

		<div id='tabs-implementation' class='tab-pane fade in active reportdiv'>
			<table id='implementation' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Property</th>
						<th>Value</th>
					</tr>
				</thead>
				<tbody>
					<?php
						// Device info	
						echo "<tr class='group'><td>Device</td><td></td></tr>";   
						generate_table("SELECT devicename(device) as Device, os as `Android version`, screenwidth as `Screen width`, screenheight as `Screen height`, cpucores as `CPU cores`, cpuspeed as `CPU speed (MHz)`, cpuarch as `CPU architecture`, submissiondate as `Submitted at`, submitter as `Submitted by` FROM reports WHERE ID = $reportID");

						// ES renderer
						echo "<tr class='group'><td>OpenGL ES renderer</td><td></td></tr>";   
						generate_table("SELECT GL_VENDOR, GL_RENDERER, GL_VERSION, GL_SHADING_LANGUAGE_VERSION FROM reports WHERE ID = $reportID");
						
						// EGL implementation
						echo "<tr class='group'><td>EGL implementation</td><td></td></tr>";   
						generate_table("SELECT EGL_VENDOR, EGL_VERSION,
							(select GROUP_CONCAT(name) from reports_eglclientapis Tlookup join egl_clientapis Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID) as 'Client APIs'
							FROM reports WHERE ID = $reportID");
							
						// ES 2.0 capabilities
						echo "<tr class='group'><td>OpenGL ES 2.0 capabilities</td><td></td></tr>";   
						if ($esversion_major >= 2) {
							generate_caps_table("SELECT * from reports_es20caps where ReportID = $reportID", 2);
						} else {
							echo "<tr><td class='subkey' style='color:#FF0000;'>not supported</td><td></td></tr>";
						}

						// ES 3.0 capabilities
						echo "<tr class='group'><td>OpenGL ES 3.0 capabilities</td><td></td></tr>";   
						if ($esversion_major >= 3) {
							generate_caps_table("SELECT * from reports_es30caps where ReportID = $reportID", 3);
						} else {
							echo "<tr><td class='subkey' style='color:#FF0000;'>not supported</td><td></td></tr>";
						} 
						
						if (($esversion_major >= 3) && ($reportversion >= 6)) {
							// ES 3.1 capabilities
							echo "<tr class='group'><td>OpenGL ES 3.1 capabilities</td><td></td></tr>";   
							if ($esversion_minor >= 1) {
								generate_caps_table("SELECT * from reports_es31caps where ReportID = $reportID", 3);
							} else {
								echo "<tr><td class='subkey' style='color:#FF0000;'>not supported</td><td></td></tr>";
							}       
							// ES 3.2 capabilities
							echo "<tr class='group'><td>OpenGL ES 3.2 capabilities</td><td></td></tr>";   
							if ($esversion_minor >= 2) {
								generate_caps_table("SELECT * from reports_es32caps where ReportID = $reportID", 3);
							} else {
								echo "<tr><td class='subkey' style='color:#FF0000;'>not supported</td><td></td></tr>";
							}       
						}
					?>	    
				</tbody>
			</table>
		</div>

		<!-- GL Extensions -->

		<div id='tabs-gl-extensions' class='tab-pane fade in reportdiv'>
			<table id='extensions' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Extension</th>
					</tr>
				</thead>
				<tbody>
					<?php	
						generate_list_table("SELECT name from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid = $reportID", 'OpenGL ES Extensions', './listreports.php?extension=');	
					?>	
				</tbody>
			</table>
		</div>

		<!-- EGL Extensions -->

		<div id='tabs-egl-extensions' class='tab-pane fade reportdiv'>
			<table id='eglextensions' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Extension</th>
					</tr>
				</thead>
				<tbody>
					<?php	
					generate_list_table(
						"SELECT name from reports_eglextensions Tlookup join egl_extensions Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID", 
						'EGL Extensions', 
						'./listreports.php?eglextension=');
					?>		
				</tbody>
			</table>
		</div>

		<!-- Commpressed formats -->

		<div id='tabs-compressedformats' class='tab-pane fade reportdiv'>
			<table id='compressedformats' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Format</th>
					</tr>
				</thead>
				<tbody>
				<?php
					generate_list_table(
						"SELECT name from reports_compressedformats rcf join compressedformats cf on rcf.compressedformatid = cf.id where rcf.reportid = $reportID and cf.name != '0x0'", 
						'Compressed texture formats', 
						'./listreports.php?compressedtextureformat=');
				?>
				</tbody>
			</table>
		</div>
		
		<!-- Shader formats -->
		<div id='tabs-shaderformats' class='tab-pane fade reportdiv'>

			<!-- Binary shader formats -->
			<h3>Binary shader formats</h3>
			<table id='shaderformats' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Format</th>
					</tr>
				</thead>
				<tbody>
					<?php	
						generate_list_table(
							"SELECT name from reports_binaryshaderformats rbsf join binaryshaderformats sf on rbsf.binaryshaderformatid = sf.id where rbsf.reportid = $reportID", 
							'Binary shader formats', '');
					?>
				</tbody>
			</table>

			<!-- Binary program formats -->
			<h3>Binary program formats</h3>
			<table id='programformats' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Format</th>
					</tr>
				</thead>
				<tbody>
					<?php	
						generate_list_table(
							"SELECT name from reports_binaryprogramformats Tlookup join binaryprogramformats Tjoin on Tlookup.ID = Tjoin.id where Tlookup.reportid = $reportID", 
							'Binary program formats', '');
					?>
				</tbody>
			</table>

		</div>
			
		<!-- Sensors -->
		<div id='tabs-sensors' class='tab-pane fade reportdiv'>
			<table id='sensors' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Name</th>
						<th>Max.Range</th>
						<th>Resolution</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$stmnt = DB::$connection->prepare("SELECT name,maxrange,resolution from reports_sensors where ReportID = :reportid");
						$stmnt->execute(["reportid" => $reportID]);
						$extarray = array();
						while($row = $stmnt->fetch(PDO::FETCH_NUM)) {	
							echo "<tr>";
							echo "<td class='valuezeroleft'>".$row[0]."</td>";
							echo "<td class='valuezeroleft'>".$row[1]."</td>";
							echo "<td class='valuezeroleft'>".$row[2]."</td>";
							echo "</tr>";
						}
					?>					
				</tbody>
			</table>
		</div>
		
		<!-- Features -->
		<div id='tabs-features' class='tab-pane fade reportdiv'>
			<table id='features' class='table table-striped table-bordered table-hover responsive' style='width:100%;'>
				<thead>
					<tr>
						<th>Device features</th>
					</tr>
				</thead>
				<tbody>
					<?php generate_list_table("SELECT devicefeature from reports_devicefeatures Tlookup join devicefeatures Tjoin on Tlookup.DEVICEFEATUREID = Tjoin.id where Tlookup.reportid = $reportID", '', ''); ?>
				</tbody>
			</table>
		</div>
    
	</div>

<?php	
	DB::disconnect();    
	include "footer.html";
?>     

	<script>
    	$(document).ready(function() 
        {
            var tableNames = [ "#implementation", "#extensions", '#eglextensions', '#compressedformats', '#sensors', '#features', '#shaderformats', '#programformats' ];
	        for (var i=0; i < tableNames.length; i++) 
            {           
                $(tableNames[i]).DataTable({
					"pageLength" : -1,
					"paging" : false,
					"order": [], 
					"searchHighlight": true,
					"bAutoWidth": false,
					"sDom": 'flpt',
					"deferRender": true,
					"processing": true,				
                    "searchHighlight": true
                });
            }
		} );	
	</script>    

</center>	   
</body>
</html>