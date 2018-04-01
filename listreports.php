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

	$negate = false;
    $searchType = '';
	$headeradd = '';
	
	// Header
	$defaultHeader = true;
	$alertText = null;	
    $negate = false;
	if (isset($_GET['option'])) {
		if ($_GET['option'] == 'not') {
			$negate = true;
		}
	}

	// Filters
	$filter["extension"] = null;
	if ($_GET['extension'] != '') {
		$filter["extension"] = $_GET['extension'];
		$defaultHeader = false;
		$headerClass = $negate ? "header-red" : "header-green";			
		$caption = "Reports ".($negate ? "<b>not</b>" : "")." supporting <b>".$filter["extension"]."</b>";	
		$caption .= " (<a href='listreports.php?extension=".$filter["extension"].($negate ? "" : "&option=not")."'>toggle</a>)";
	}

	$filter["eglextension"] = null;
	if ($_GET['eglextension'] != '') {
		$filter["eglextension"] = $_GET['eglextension'];
		$defaultHeader = false;
		$headerClass = $negate ? "header-red" : "header-green";			
		$caption = "Reports ".($negate ? "<b>not</b>" : "")." supporting <b>".$filter["eglextension"]."</b>";	
		$caption .= " (<a href='listreports.php?eglextension=".$filter["eglextension"].($negate ? "" : "&option=not")."'>toggle</a>)";
	}	

	$filter["compressedtextureformat"] = null;
    if($_GET['compressedtextureformat'] != '') {
		$filter["compressedtextureformat"] = $_GET['compressedtextureformat'];
		$defaultHeader = false;
		$headerClass = $negate ? "header-red" : "header-green";			
		$caption = "Reports ".($negate ? "<b>not</b>" : "")." supporting format <b>".$filter["compressedtextureformat"]."</b>";	
		$caption .= " (<a href='listreports.php?compressedtextureformat=".$filter["compressedtextureformat"].($negate ? "" : "&option=not")."'>toggle</a>)";
	}

	$filter["devicefeature"] = null;
    if($_GET['devicefeature'] != '') {
		$filter["devicefeature"] = $_GET['devicefeature'];
		$defaultHeader = false;
		$headerClass = $negate ? "header-red" : "header-green";			
		$caption = "Reports ".($negate ? "<b>not</b>" : "")." supporting feature <b>".$filter["devicefeature"]."</b>";	
		$caption .= " (<a href='listreports.php?devicefeature=".$filter["devicefeature"].($negate ? "" : "&option=not")."'>toggle</a>)";
    }

	$filter["submitter"] = null;
    if($_GET['submitter'] != '') {
		$filter["submitter"] = $_GET['submitter'];
		$defaultHeader = false;
		$headerClass = "header-blue";
		$caption = "Reports submitted by <b>".$filter["submitter"]."</b>";	
	}
	
	$filter["capability"] = null;
    if (($_GET['capability'] != '') && ($_GET['esversion'] != '') && ($_GET['value'] != '')) {
		$filter["capability"] = $_GET['capability'];
		$filter["capabilityesversion"] = $_GET['esversion'];
		$filter["capabilityvalue"] = $_GET['value'];
		$defaultHeader = false;
		$headerClass = "header-info";
		$link = "displaycapability.php?name=".$filter["capability"]."&esversion=".$filter["capabilityesversion"];
		$caption = "Reports with <a href=".$link.">".$filter["capability"]."</a> (OpenGL ES.".$filter["capabilityesversion"].".0) = ".$filter["capabilityvalue"];	
	}

	if ($defaultHeader) {
		echo "<div class='header'>";	
		echo "	<h4>Listing reports</h4>";
		echo "</div>";		
	}			
?>

	<center>
	<div class="tablediv">
		<form method="get" action="compare.php?">
			<table id="reports" class="table table-striped table-bordered table-hover reporttable" style="width:auto">
				<?php
					if (!$defaultHeader) {
						echo "<caption class='".$headerClass." header-span'>".$caption."</caption>";
					}
				?>				
				<thead>
					<tr>
						<th></th>
						<th>Name</th>
						<th>GLES</th>
						<th>SL</th>
						<th>Renderer</th>
						<th>Android</th>
						<th>Date</th>
						<th><input type='submit' name='compare' value='compare' class='button'></th>
					</tr>
					<tr>
						<th>id</th>					
						<th>Name</th>
						<th>GLES</th>
						<th>SL</th>
						<th>Renderer</th>
						<th>Android</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
			</table>
			<div id="errordiv" style="color:#D8000C;"></div>		
		</form>
		</div>
					
	<script>
		$( document ).ready(function() {

			var table = $('#reports').DataTable({
				"processing": true,
				"serverSide": true,
				"paging" : true,		
				"searching": true,	
				"lengthChange": false,
				"dom": 'lrtip',	
				"pageLength" : 25,		
				"order": [[ 0, 'desc' ]],
				"columnDefs": [
					{ 
						"searchable": false, "targets": [ 0, 7 ],
						"orderable": false, "targets": 7,
					}
				],
				"ajax": {
					url :"backend/reports.php",
					data: {
						"filter": {
							'option' : '<?php echo $_GET["option"] ?>',
							'extension' : '<?php echo $filter["extension"] ?>',
							'eglextension' : '<?php echo $filter["eglextension"] ?>',
							'compressedtextureformat': '<?php echo $filter["compressedtextureformat"] ?>',
							'devicefeature': '<?php echo $filter["devicefeature"] ?>',
							'submitter': '<?php echo $filter["submitter"] ?>',
							'capability': '<?php echo $filter["capability"] ?>',
							'capabilityesversion': '<?php echo $filter["capabilityesversion"] ?>',
							'capabilityvalue': '<?php echo $filter["capabilityvalue"] ?>',
						}
					},
					error: function (xhr, error, thrown) {
						$('#errordiv').html('Could not fetch data (' + error + ')');
						$('#reports_processing').hide();
					}				
				},
				"columns": [
					{ data: 'id' },
					{ data: 'name' },
					{ data: 'glesversion' },
					{ data: 'slversion' },
					{ data: 'renderer' },
					{ data: 'os' },
					{ data: 'date' },
					{ data: 'compare' },				
				],
				// Pass order by column information to server side script
				fnServerParams: function(data) {
					data['order'].forEach(function(items, index) {
						data['order'][index]['column'] = data['columns'][items.column]['data'];
					});
				},
			});   

			// Per-Column filter boxes
			$('#reports thead th').each( function (i) {
				var title = $('#reports thead th').eq( $(this).index() ).text();
				if ((title !== 'id') && (title !== '')) {
					var w = (title != 'Name') ? 120 : 240;
					$(this).html( '<input type="text" placeholder="'+title+'" data-index="'+i+'" style="width: '+w+'px;" class="filterinput" />' );
				}
			}); 
			$(table.table().container() ).on('keyup', 'thead input', function () {
				table
					.column($(this).data('index'))
					.search(this.value)
					.draw();
			});		

		});
	</script>
	<?php include "footer.html"; ?>
	</center>	
</body>
</html>