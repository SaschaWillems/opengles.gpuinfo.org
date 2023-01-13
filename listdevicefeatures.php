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
 
	include 'header.php';
	include 'dbconfig.php';	
	
	DB::connect();
 
	$featureCount = DB::getCount("SELECT count(*) from viewDeviceFeatures", []);
	$reportCount = DB::getCount("SELECT count(*) from reports", []);
?>

	<script>
		$(document).ready(function() {
			var table = $('#features').DataTable({
				"pageLength" : 50,
				"paging" : true,
				"stateSave": false, 
				"searchHighlight" : true,	
				"dom": 'fp',			
				"bInfo": false,	
				"order": [[ 0, "asc" ]]	
			});
		} );	
	</script>

	<div class='header'>
		<h4 style='margin-left:10px;'>Listing all device features (<?php echo $featureCount ?>)</h4>
	</div>

<center>	
	<div class='parentdiv'>
		<div class='tablediv' style='width:auto; display: inline-block;'>	
			<table id="features" class="table table-striped table-bordered table-hover reporttable" >
				<thead>
					<tr>			
						<th>Extension</th>
						<th>Coverage</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						$stmnt = DB::$connection->prepare("SELECT name, reports from viewDeviceFeatures");
						$stmnt->execute();			
						while ($row = $stmnt->fetch(PDO::FETCH_NUM)) {
							echo "<tr>";						
							echo "<td class='firstcolumn'><a href='listreports.php?devicefeature=".$row[0]."'>".$row[0]."</a></td>";					
							echo "<td class='firstcolumn' align=center>".round(($row[1] / $reportCount * 100.0), 2)."%</td>";
							echo "</tr>";	    
						}            			
					?>   
				</tbody>
			</table> 
		</div>
	</div>
</center>
<?php 
	DB::disconnect();
	include "footer.html";
?>
</body>
</html>