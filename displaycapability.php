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
	
	$name = null;
	$esversion = null;
	if (isset($_GET['name'])) {
		$name = $_GET['name'];
	}
	if (isset($_GET['es'])) {
		$esversion = $_GET['es'];
	}
	$tablename = "reports_es20caps";
	if ($esversion === "3") {
		$tablename = "reports_es30caps";								
	}														

	// Check if capability as valid and part of the selected table
	DB::connect();
	$result = DB::$connection->prepare("SELECT * from information_schema.columns where TABLE_NAME= :tablename and column_name = :columnname");
	$result->execute([":columnname" => $name, ":tablename" => $tablename]);
	DB::disconnect();
	if ($result->rowCount() == 0) {
		echo "<center>";
		?>
			<div class="alert alert-danger error">
			<strong>This is not the <strike>droid</strike> capability you are looking for!</strong><br><br>
			You may have passed a wrong capability name or selected the wrong OpenGL ES target version.
			</div>				
		<?php
		include "footer.html";
		echo "</center>";
		die();		
	}
?>

	<script>
		$(document).ready(function() {
			var table = $('#extensions').DataTable({
				"pageLength" : -1,
				"paging" : false,
				"stateSave": false, 
				"searchHighlight" : true,	
				"dom": '',			
				"bInfo": false,	
				"order": [[ 0, "asc" ]]	
			});
		} );	
	</script>

	<div class='header'>
		<h4 style='margin-left:10px;'>Capability summary <?php echo $name ?></h4>
	</div>

	<center>	
		<div class='parentdiv'>
			<div class='tablediv' style='width:auto; display: inline-block;'>	

				<table id="extensions" class="table table-striped table-bordered table-hover reporttable" >
					<thead>
						<tr>				
							<th>Value</th>
							<th>Reports</th>
						</tr>
					</thead>
					<tbody>				
						<?php		
							DB::connect();			
							// TODO: Check if name is valid column name (security!)
							$result = DB::$connection->prepare("SELECT $name as value, count(0) as reports from $tablename where $name > 0 group by 1 order by 1");
							$result->execute();
							$rows = $result->fetchAll(PDO::FETCH_ASSOC);
							foreach ($rows as $cap) {
								echo "<tr>";						
								echo "<td>".$cap["value"]."</td>";
								echo "<td>".$cap["reports"]."</td>";
								echo "</tr>";	    
							}     
							DB::disconnect();       			
						?>   					
					</tbody>
				</table> 

			</div>
		</div>
	</center>
	
	<?php 
		include "footer.html";
	?>

</body>
</html>