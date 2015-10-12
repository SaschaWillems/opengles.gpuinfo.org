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
	
	$sqlResult = mysql_query("select count(*) from viewExtensions");
	$sqlCount = mysql_result($sqlResult, 0);
	echo "<div class='header'>";
		echo "<h4 style='margin-left:10px;'>Listing all available extensions ($sqlCount)</h4>";
	echo "</div>";			
?>

<center>	
	<div class="reportdiv">
	<table id="extensions" class="table table-striped table-bordered table-hover reporttable" >
		<?php		
		
            $sqlstr = "select name, coverage from viewExtensions";                
			$sqlresult = mysql_query($sqlstr) or die(mysql_error());  
			
			echo "<thead><tr>";  
			
			$sortby = $_GET['sortby'];				
			echo "<td>Extension</td>";		   
			echo "<td>Coverage</td>";		   
			echo "</tr></thead><tbody>";

			while ($row = mysql_fetch_row($sqlresult))
            {
                $extname = $row[0];
				if (!empty($extname)) 
                {
					$link = str_replace("GL_", "", $extname);
					$extparts = explode("_", $link);
					$vendor = $extparts[0];
					$link = str_replace($vendor."_", "", $link);						
					echo "<tr>";						
					echo "<td class='firstcolumn'><a href='gles_listreports.php?extension=".$extname."'>".$extname."</a> (<a href='gles_listreports.php?extensionunsupported=".$extname."'>not</a>) [<a href='http://www.khronos.org/registry/gles/extensions/$vendor/$link.txt' target='_blank' title='Show specification for this extensions'>?</a>]</td>";
					echo "<td class='firstcolumn' align=center>".round(($row[1]), 2)."%</td>";
					echo "</tr>";	    
					$index++;
				}
            }            			
			dbDisconnect();	
		?>   
	</tbody>
</table> 

<script>
	$(document).ready(function() {
		$('#extensions').DataTable({
			"pageLength" : -1,
			"paging" : false,
			"stateSave": false, 
			"searchHighlight" : true
		});
	} );	
</script>
</div>
</center>
<?php include("./gles_footer.inc") ?>
</body>
</html>