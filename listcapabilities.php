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
	include 'serverconfig/gles_config.php';	
	
	dbConnect();  
	
	$sqlResult = mysql_query("SELECT count(*) FROM reports") or die();
	$reportcount = mysql_result($sqlResult, 0);	
?>

	<script>
		$(document).ready(function() {
			var table = $('#capabilities').DataTable({
				"pageLength" : 50,
				"paging" : true,
				"stateSave": false, 
				"searchHighlight" : true,	
				"dom": 'fp',			
				"bInfo": false,	
				"order": [[ 2, "asc" ], [ 0, "asc" ]],
				"deferRender": true,
				"processing": true,
				"columnDefs": [
						{ "visible": false, "targets": 2 }
				],					
				"drawCallback": function (settings) {
					var api = this.api();
					var rows = api.rows( {page:'current'} ).nodes();
					var last = null;
					api.column(2, {page:'current'} ).data().each( function ( group, i ) {
						if ( last !== group ) {
							$(rows).eq( i ).before(
								'<tr><td colspan="2" class="group">'+group+'</td></tr>'
							);
							last = group;
						}
					});
				}					
			});
		} );	
	</script>

	<div class='header'>
		<h4>Listing all available GLES capabilities</h4>
	</div>

	<center>	
		<div class='parentdiv'>
			<div class='tablediv' style='width:auto; display: inline-block;'>	

				<table id="capabilities" class="table table-striped table-bordered table-hover reporttable" >
					<thead>
						<tr>				
							<th>Capability name</th>
							<th>Coverage</th>
							<th>GL ES</th>
						</tr>
					</thead>
					<tbody>		
						<!-- GL ES 2.0 -->
						<?php										
							$sqlresult = mysql_query("SELECT column_name from information_schema.columns where TABLE_NAME='reports_es20caps' and column_name != 'reportid'") or die(mysql_error());  							
							while ($row = mysql_fetch_row($sqlresult)) {
								$sqlResult = mysql_query("SELECT count(*) FROM reports_es20caps WHERE `$row[0]` != 0") or die(mysql_error());  	
								$sqlCount = mysql_result($sqlResult, 0);												
								echo "<tr>";						
								echo "<td class='subkey'><a href='displaycapability.php?name=$row[0]&esversion=2'>$row[0]</a></td>";
								echo "<td align=center>".round($sqlCount / $reportcount * 100, 1)."%</td>";
								echo "<td align='center'>OpenGL ES 2.0</td>";
								echo "</tr>";	    
							}            			
						?>   					
						<!-- GL ES 3.0 -->
						<?php										
							$sqlresult = mysql_query("SELECT column_name from information_schema.columns where TABLE_NAME='reports_es30caps' and column_name != 'reportid'") or die(mysql_error());  							
							while ($row = mysql_fetch_row($sqlresult)) {
								$sqlResult = mysql_query("SELECT count(*) FROM reports_es30caps WHERE `$row[0]` != 0") or die(mysql_error());  	
								$sqlCount = mysql_result($sqlResult, 0);												
								echo "<tr>";						
								echo "<td class='subkey'><a href='displaycapability.php?name=$row[0]&esversion=3'>$row[0]</a></td>";
								echo "<td align=center>".round($sqlCount / $reportcount * 100, 1)."%</td>";
								echo "<td align='center'>OpenGL ES 3.0</td>";
								echo "</tr>";	    
							}            			
						?>   					
					</tbody>
				</table> 

			</div>
		</div>
	</center>
	
	<?php 
		dbDisconnect();		
		include "footer.html";
	?>

</body>
</html>