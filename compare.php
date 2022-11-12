<?php
     /*
		*
		* OpenGL ES hardware capability database server implementation
		*
		* Copyright (C) 2013-2022 by Sascha Willems (www.saschawillems.de)
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
	
	// Compare selected reports
	$reportids = array();
	$devicenames = array();
	$reportlimit = false;

	// Get checked report IDs
	foreach ($_GET as $k => $v) {
		if (!is_numeric($k)) 
			continue;
		$reportids[] = $k;	
		if (count($reportids) > 7) 
		{ 
			$reportlimit = true;	 
			break; 
		}
	}   

	// Compare from report list (new format)
	// The URL contains a comma separated list of report ids dot compare
	// e.g. compare.php/reports=100,200,900
	if (isset($_REQUEST['reports'])) {
		$params = explode(',', $_REQUEST['reports']);
		foreach($params as $param) {
			if (is_numeric($param)) {
				$reportids[] = intval($param);
			}
		}
	}	
	
	if ($reportlimit) {
		echo "<b>Note : </b>You selected more than 8 reports to compare, only displaying 8 reports.\n";
	}	

	sort($reportids, SORT_NUMERIC);

	// Get device names
	$repids = implode(",", $reportids);   
	$stmnt = DB::$connection->prepare("SELECT devicename_short(device) as device FROM reports WHERE ID IN (" . $repids . ")");
	$stmnt->execute();
	while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
		$devicenames[] = $row[0];
	}
?>	

    <div class='header'>
		<h4 style='margin-left:10px;'>Comparing <?php echo implode(", ", $devicenames); ?></h4>
		<label id="toggle-label" class="checkbox-inline" style="display:none;">
			<input id="toggle-event" type="checkbox" data-toggle="toggle" data-size="small" data-onstyle="success"> Display only different values
		</label>
	</div>

<?php
	function generate_table($sql) {	
		$columns = array();
		$captions = array();
		
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
			$columns[] = $reportdata; 
		}
		
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($columns[0]); $i < $arrsize; ++$i) { 	  
			echo "<tr>";
			echo "<td class='fieldcaption'>".$captions[$i]."</td>\n";
			for ($j = 0, $subarrsize = sizeof($columns); $j < $subarrsize; ++$j) {	 
				echo "<td class='value'>".$columns[$j][$i]."</td>";
			} 
			echo "</tr>";
			$rowindex++;
		}   
			
	}
	
	function generate_caps_table($sql, $esversion) 
	{	
		$columns = array();
		$captions = array();
	
		$stmnt = DB::$connection->prepare($sql);
		$stmnt->execute();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			$colindex = 0;
			$reportdata = array();		
			foreach ($row as $data)	{			
				$reportdata[] = $data;	  
				$meta = $stmnt->getColumnMeta($colindex);
				$captions[] = $meta["name"];
				$colindex++;
			} 
			$columns[] = $reportdata; 
		}		
								
		// Generate table from array
		$rowindex = 0;
		for ($i = 0, $arrsize = sizeof($columns[0]); $i < $arrsize; ++$i) { 	  
			if ($captions[$i] != 'REPORTID') {
				$className = 'same';
				// Get max and min values
				if (is_numeric($columns[0][$i])) {
					$minval = $columns[0][$i];
					$maxval = $columns[0][$i];					
					for ($j = 0, $subarrsize = sizeof($columns); $j < $subarrsize; ++$j) {	 			
						if ($columns[$j][$i] < $minval) {
							$minval = $column[$j][$i];
						}
						if ($column[$j][$i] > $maxval) {
							$maxval = $columns[$j][$i];
						}
					}
				}		
				if ($minval < $maxval) {
					$className = 'diff';
				}
			
				echo "<tr class='$className'>";
				echo "<td class='fieldcaption'>".$captions[$i]."</td>";
				for ($j = 0, $subarrsize = sizeof($columns); $j < $subarrsize; ++$j) {	 				
					$valClassName = ($className == 'diff') ? "maxvalue" : "";
					if (is_numeric($columns[$j][$i])) {
						if ($columns[$j][$i] < $maxval) {
							$valClassName = "lowervalue";
						}
					}						
					echo "<td class='value $valClassName'>".number_format($columns[$j][$i], 0, '.', ',')."</td>";
				} 
				echo "</tr>";
			}
			$rowindex++;
		}   		
	}
	
	function generate_caps_tables($reportids) 
	{	
		$repids = implode(",", $reportids);
		$colspan = count($reportids) + 1;
		generate_caps_table("SELECT * FROM reports_es20caps WHERE ReportID IN (" . $repids . ")", 2);		
		
		$es3 = false;
		$stmnt = DB::$connection->prepare("SELECT ESVERSION_MAJOR FROM reports WHERE ID in (" . $repids . ")");
		$stmnt->execute();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			$esversion = $row[0];
			if ($esversion >= 3) {
				$es3 = true;
			}
		}
		echo "</tr>";
		
		if ($es3) {
			generate_caps_table("SELECT * FROM reports_es30caps WHERE ReportID IN (" . $repids . ")", 3);		
		}
	
	}
	
	function generate_extension_table($reportids, $sql, $checksql, $caption = "Extension") {
		$extcaptions = array(); 
		$stmnt = DB::$connection->prepare($sql);
		$stmnt->execute();
		while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
			foreach ($row as $data) {
				$extcaptions[] = $data;	  
			}
		}

		$extarray   = array(); 
		foreach ($reportids as $repid) {
			$stmnt = DB::$connection->prepare($checksql."= $repid");
			$stmnt->execute();
			$subarray = array();
			while($row = $stmnt->fetch(PDO::FETCH_NUM)) {
				foreach ($row as $data) {
					$subarray[] = $data;	  
				}
			}
			$extarray[] = $subarray; 
		}
		
		global $devicenames;
        echo "<thead><tr>";
		echo "<th>".$caption."</th>"; 
		for ($i = 0; $i < sizeof($devicenames); $i++) { 	  
			echo "<th>".$devicenames[$i]."</th>";
		}
        echo "</tr></thead><tbody>";
        
		// Extension count 	
		echo "<tr><td class='value'>count</td>"; 
		for ($i = 0, $arrsize = sizeof($extarray); $i < $arrsize; ++$i) { 	  
			echo "<td class='value'>".count($extarray[$i])."</td>";
		}
		echo "</tr>"; 			
		
		// Generate table
		$arrcount = count($extcaptions);
		if ($arrcount > 0) {
			$colspan = count($reportids) + 1;	
			$rowindex = 0;
			foreach ($extcaptions as $extension) {			
				if ($extcaptions === '0x0') {
					continue;
				}
				// Check if missing in at least one report
				$missing = false;
				$index = 0;
				foreach ($reportids as $repid) {
					if (!in_array($extension, $extarray[$index])) 
					{ 
						$missing = true;
					}
					$index++;
				}  			

				$color = ($missing) ? '#FF0000' : '#000000';		
				$className = "same";
				$index = 0;
				foreach ($reportids as $repid) {
					if (!in_array($extension, $extarray[$index])) 
					{ 
						$className = "diff";
					}
					$index++;
				}				
				$index = 0;
				echo "<tr class='$className'><td class='fieldcaption'>$extension</td>";		 
				foreach ($reportids as $repid) {
					echo "<td class='value' style='margin-left:10px;'><span class='glyphicon ".(in_array($extension, $extarray[$index]) ? "glyphicon-ok supported" : "glyphicon-remove unsupported")."'></td>";
					$index++;
				}  
				$rowindex++;
			echo "</tr>\n"; 	
			}
		}	
	}
?>
    <center>

	<!-- Nav  -->
	<div id='tabs'>
    <ul class='nav nav-tabs'>
    	<li class='active'><a data-toggle='tab' href='#tabs-implementation'>Implementation</a></li>
    	<li><a data-toggle='tab' href='#tabs-es-ext'>GL Extensions</a></li>
    	<li><a data-toggle='tab' href='#tabs-egl-ext'>EGL Extensions</a></li>
    	<li><a data-toggle='tab' href='#tabs-compressed-formats'>Compr. Formats</a></li>
    	<li><a data-toggle='tab' href='#tabs-shader-formats'>Shader Formats</a></li>
	</ul>
	<div class='tablediv tab-content' style='width:75%;'>
	
    <!-- Implementation -->
    <div id='tabs-implementation' class='tab-pane fade in active reportdiv'>
		<table id='implementation' width='100%' class='table table-striped table-bordered table-hover'>
			<thead>
				<tr>
					<th>Capability</th>
					<?php	
						for ($i = 0; $i < sizeof($devicenames); $i++) {
							echo "<th>".$devicenames[$i]."</th>";
						}
					?>
				</tr>
			</thead>
			<tbody>				
				<!-- Basic device information -->
				<?php generate_table("SELECT os as `Android version`, screenwidth as `Screen width`, screenheight as `Screen height`, cpucores as `CPU cores`, cpuspeed as `CPU speed (MHz)`, cpuarch as `CPU architecture`, submissiondate as `Submitted at`, submitter as `Submitted by` FROM reports WHERE ID IN (" . $repids . ")");  ?>
				<!-- OpenGL ES info -->
				<?php generate_table("SELECT GL_VENDOR, GL_RENDERER, GL_VERSION, GL_SHADING_LANGUAGE_VERSION FROM reports WHERE ID IN (" . $repids . ")"); ?>
				<!-- OpenGL ES caps -->
				<?php generate_caps_tables($reportids); ?>
			</tbody>
		</table>
	</div>
	
	<!-- Extensions -->	
    <div id='tabs-es-ext' class='tab-pane fade reportdiv'>
		<table id='extensions' width='100%' class='table table-striped table-bordered table-hover'>
			<?php generate_extension_table(
					$reportids,
					"SELECT DISTINCT name from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid IN (" . $repids . ")",
					"SELECT name from reports_extensions rext join extensions ext on rext.extensionid = ext.id where rext.reportid");	
			?>
		</table>
	</div>

	
    <!-- EGL Extensions -->
    <div id='tabs-egl-ext' class='tab-pane fade reportdiv'>
		<table id='extensionsegl' width='100%' class='table table-striped table-bordered table-hover'>
			<?php generate_extension_table(
				$reportids,
				"SELECT DISTINCT name from reports_eglextensions rext join egl_extensions ext on rext.id = ext.id where rext.reportid IN (" . $repids . ")",
				"SELECT name from reports_eglextensions rext join egl_extensions ext on rext.id = ext.id where rext.reportid");									 
			?>
    	</table>
	</div>
	
	<!-- Compressed texture formats -->
    <div id='tabs-compressed-formats' class='tab-pane fade reportdiv'>
		<table id='compressedformats' width='100%' class='table table-striped table-bordered table-hover'>
			<?php generate_extension_table(
				$reportids,
				"SELECT DISTINCT name from reports_compressedformats rcr join compressedformats cr on rcr.compressedformatid = cr.id where rcr.reportid IN (" . $repids . ")",
				"SELECT name from reports_compressedformats rcf join compressedformats cf on rcf.compressedformatid = cf.id where rcf.reportid",
				"Compressed format"
				);
			?>
    	</table>
	</div>

	<!-- Shader formats -->
    <div id='tabs-shader-formats' class='tab-pane fade reportdiv'>
	<!-- Binary shader formats -->
		<table id='shaderformats' width='100%' class='table table-striped table-bordered table-hover'>
			<?php generate_extension_table(
				$reportids,
				"SELECT DISTINCT name from reports_binaryshaderformats bsf join binaryshaderformats bf on bsf.BINARYSHADERFORMATID = bf.ID where bsf.reportid IN (" . $repids . ")",
				"SELECT name from reports_binaryshaderformats bsf join binaryshaderformats bf on bsf.BINARYSHADERFORMATID = bf.id where bsf.reportid",
				"Shader format");	
			?>							
    	</table>

		<!-- Binary program formats -->
		<table id='programformats' width='100%' class='table table-striped table-bordered table-hover'>
			<?php generate_extension_table($reportids,
				"SELECT DISTINCT name from reports_binaryprogramformats bpf join binaryprogramformats bp on bpf.ID = bp.ID where bpf.reportid IN (" . $repids . ")",
				"SELECT name from reports_binaryprogramformats bpf join binaryprogramformats bp on bpf.ID = bp.ID where bpf.reportid",
				"Program format"
				);
			?>
    	</table>
	</div>

	</center>
	
	<?php 
		DB::disconnect();
		include "footer.html"; 
	?>
	       
	<script>
    	$(document).ready(function() 
        {
            var tableNames = [ "#implementation", "#extensions", "#extensionsegl", "#compressedformats", "#shaderformats", "#programformats" ];
	        for (var i=0; i < tableNames.length; i++) 
            {           
                $(tableNames[i]).DataTable({
					"pageLength" : -1,
					"paging" : false,
					"order": [], 
					"searchHighlight": true,
					"sDom": 'flpt',
					"deferRender": true							
                });
            }
			$("#toggle-label").show();			
		} );	

		$('#toggle-event').change(function() {
			if ($(this).prop('checked')) {
				$('.same').hide();
				$('.sameCaps').hide();
			} else {
				$('.same').show();				
				$('.sameCaps').show();
			}
		} );
	</script>
    
</body>
</html>