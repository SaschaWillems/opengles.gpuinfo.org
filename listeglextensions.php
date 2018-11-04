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
 
	$extensionsCount = DB::getCount("SELECT count(*) from viewEGLExtensions", []);
	$reportCount = DB::getCount("SELECT count(*) from reports", []);	
?>

	<script>
		$(document).ready(function() {
			var table = $('#extensions').DataTable({
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
		<h4 style='margin-left:10px;'>Listing all available EGL extensions (<?php echo $extensionsCount ?>)</h4>
	</div>

	<center>	
		<div class='parentdiv'>
			<div class='tablediv' style='width:auto; display: inline-block;'>	

				<table id="extensions" class="table table-striped table-bordered table-hover reporttable" >
					<thead>
						<tr>				
							<th>Extension</th>
							<th>Coverage</th>
						</tr>
					</thead>
					<tbody>				
						<?php					
							$stmnt = DB::$connection->prepare("SELECT name, reports from viewEGLExtensions");
							$stmnt->execute();			
							while ($row = $stmnt->fetch(PDO::FETCH_NUM)) {						
								$extname = $row[0];
								if (!empty($extname)) {			
									echo "<tr>";						
									echo "<td class='firstcolumn'><a href='listreports.php?eglextension=".$extname."'>".$extname."</a> (<a href='listreports.php?eglextension=".$extname."&option=not'>not</a>)</td>";
									echo "<td class='firstcolumn' align=center>".round(($row[1] / $reportCount * 100.0), 2)."%</td>";
									echo "</tr>";	    
									$index++;
								}
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