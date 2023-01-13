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
 
	$extensionsCount = DB::getCount("SELECT count(*) from viewExtensions", []);
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
		<h4 style='margin-left:10px;'>Listing all available GLES extensions (<?php echo $extensionsCount ?>)</h4>
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
							$stmnt = DB::$connection->prepare("SELECT name, reports from viewExtensions");
							$stmnt->execute();			
							while ($row = $stmnt->fetch(PDO::FETCH_NUM)) {
								$extname = $row[0];
								if (!empty($extname)) {
									$link = str_replace("GL_", "", $extname);
									$extparts = explode("_", $link);
									$vendor = $extparts[0];
									$link = str_replace($vendor."_", "", $link);						
									echo "<tr>";						
									echo "<td class='firstcolumn'><a href='listreports.php?extension=".$extname."'>".$extname."</a> (<a href='listreports.php?extension=".$extname."&option=not'>not</a>) [<a href='http://www.khronos.org/registry/gles/extensions/$vendor/$link.txt' target='_blank' title='Show specification for this extensions'>?</a>]</td>";
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