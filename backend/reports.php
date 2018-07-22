<?php
	/*
		*
		* OpenGL ES hardware capability database server implementation
		*
		* Copyright (C) 2011-2018 by Sascha Willems (www.saschawillems.de)
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

    include '../dbconfig.php';

    function shorten($string, $length) {
        return (strlen($string) >= $length) ? substr($string, 0, $length-10). " ... " . substr($string, -5) : $string;
    }

    DB::connect();

    $data = array();
    $params = array();    
             
    // Ordering
    $orderByColumn = '';
    $orderByDir = '';
    if (isset($_REQUEST['order']) && count($_REQUEST['order'] > 0)) {
        $orderByColumn = $_REQUEST['order'][0]['column'];
        $orderByDir = $_REQUEST['order'][0]['dir'];
    }

    // Paging
    $paging = '';
    if (isset($_REQUEST['start'] ) && $_REQUEST['length'] != '-1') {
        $paging = "LIMIT ".$_REQUEST["length"]. " OFFSET ".$_REQUEST["start"];
    }  

    // Pre-Filter
    $whereClause = '';
    $selectAddColumns = '';
    $negate = false;
	if (isset($_REQUEST['filter']['option'])) {
		if ($_REQUEST['filter']['option'] == 'not') {
			$negate = true;
		}
    }        
	// Filters
    // GLES Extension
	if (isset($_REQUEST['filter']['extension'])) {
	    $extension = $_REQUEST['filter']['extension'];
        if ($extension != '') {
            $whereClause = "where id ".($negate ? "not" : "")." in (select distinct(reportid) from reports_extensions rext join extensions ext on ext.id = rext.EXTENSIONID where ext.name = :filter_extension)";
            $params['filter_extension'] = $extension;
        }
    }
    // EGL Extension
	if (isset($_REQUEST['filter']['eglextension'])) {
	    $extension = $_REQUEST['filter']['eglextension'];
        if ($extension != '') {
            $whereClause = "where id ".($negate ? "not" : "")." in (select distinct(reportid) from reports_eglextensions rext join egl_extensions ext on ext.id = rext.id where ext.name = :filter_extension)";
            $params['filter_extension'] = $extension;
        }
    }

    // Compressed format
    if (isset($_REQUEST['filter']['compressedtextureformat'])) {
	    $compressedformat = $_REQUEST['filter']['compressedtextureformat'];
        if ($compressedformat != '') {
            $whereClause = "where id ".($negate ? "not" : "")." in (select distinct(reportid) from reports_compressedformats rcf join compressedformats cf on cf.id = rcf.compressedformatid where cf.name = :filter_compressedformat or cf.displayname = :filter_compressedformat)";
            $params['filter_compressedformat'] = $compressedformat;            
        }
    }   
    
    // Device feature
    if (isset($_REQUEST['filter']['devicefeature'])) {
	    $feature = $_REQUEST['filter']['devicefeature'];
        if ($feature != '') {
            $whereClause = "where id ".($negate ? "not" : "")." in (select distinct(reportid) from reports_devicefeatures rdev join devicefeatures dev on dev.id = rdev.devicefeatureid where dev.DEVICEFEATURE = :filter_devicefeature)";
            $params['filter_devicefeature'] = $feature;            
        }
    }   

    // Submitter
    if (isset($_REQUEST['filter']['submitter'])) {
	    $submitter = $_REQUEST['filter']['submitter'];
        if ($submitter != '') {
            $whereClause = "where submitter = :filter_submitter";
            $params['filter_submitter'] = $submitter;            
        }
    }

    // Capability
    if (($_REQUEST['filter']['capability'] != '') && ($_REQUEST['filter']['capabilityesversion'] != '') && ($_REQUEST['filter']['capabilityvalue'] != '')) {
        $tablename = 'reports_es20caps';
        if ($_REQUEST['filter']['capabilityesversion'] == "3") {
            $tablename = 'reports_es30caps';
        }            
        if ($_REQUEST['filter']['capabilityesversion'] == "31") {
            $tablename = "reports_es31caps";
        }														
        if ($_REQUEST['filter']['capabilityesversion'] == "32") {
            $tablename = "reports_es32caps";
        }		        
        $columnname = $_REQUEST['filter']['capability'];
		// Check if capability column exists
		$result = DB::$connection->prepare("SELECT * from information_schema.columns where TABLE_NAME= :tablename and column_name = :columnname");
		$result->execute([":columnname" => $columnname, ":tablename" => $tablename]);
        if ($result->rowCount() == 0) {
            die("Invalid capability");
        }                
        $whereClause = "where reports.id in (select reportid from $tablename where `$columnname` = :filter_capability_value)";
        $params['filter_capability_value'] = $_REQUEST['filter']['capabilityvalue'];
    }    

    // Per-Column filter
    $searchColumns = array(
        'id',
        'devicename(reports.device)', 
        'concat(reports.esversion_major, ".", reports.esversion_minor)', 
        'concat(reports.shadinglanguageversion_major, ".", reports.shadinglanguageversion_minor)', 
        'gl_renderer', 
        'os', 
        'date(reports.submissiondate)');

    // Per-column, filtering
    $filters = array();
    for ($i = 0; $i < count($_REQUEST['columns']); $i++) {
        $column = $_REQUEST['columns'][$i];
        if (($column['searchable'] == 'true') && ($column['search']['value'] != '')) {
            $filters[] = $searchColumns[$i].' like :filter_'.$i;
            $params['filter_'.$i] = $column['search']['value'].'%';
            if (($i == 1) || ($i == 4)) {
                $params['filter_'.$i] = '%'.$params['filter_'.$i];
            }
        }
    }
    if (sizeof($filters) > 0) {
        $searchClause = ($whereClause === '' ? 'where ' : 'and ').implode(' and ', $filters);
    }       

    if (!empty($orderByColumn)) {
        $orderBy = "order by ".$orderByColumn." ".$orderByDir;
    }

    if ($orderByColumn == "api") {
        $orderBy = "order by length(".$orderByColumn.") ".$orderByDir.", ".$orderByColumn." ".$orderByDir;
    }

    $columns = "
        reports.id, 
        devicename(reports.device) as name, 
        reports.os, 
        concat(reports.esversion_major, '.', reports.esversion_minor) as glesversion,
        concat(reports.shadinglanguageversion_major, '.', reports.shadinglanguageversion_minor) as slversion,
        date(reports.submissiondate) as date, 
        reports.gl_renderer as renderer";
   
    $sql = "select ".$columns." from reports ".$whereClause." ".$searchClause." ".$orderBy;

    $devices = DB::$connection->prepare($sql." ".$paging);
    $devices->execute($params);
    if ($devices->rowCount() > 0) { 
        foreach ($devices as $device) {            							
            $data[] = array(
                'id' => $device["id"], 
                'name' => '<a href="displayreport.php?id='.$device["id"].'">'.shorten($device["name"], 35).'</a>',
                'glesversion' => $device["glesversion"],
                'slversion' => $device["slversion"],
                'renderer' => shorten($device["renderer"], 24),
                'os' => $device["os"],
                'date' => $device["date"],
                'compare' => '<center><input type="checkbox" name="'.$device["id"].'" value="1"></center>'
            );
        }        
    }

    $filteredCount = 0;
    $stmnt = DB::$connection->prepare("select count(*) from reports");
    $stmnt->execute();
    $totalCount = $stmnt->fetchColumn(); 

    $filteredCount = $totalCount;
    if (($searchClause != '') or ($whereClause != ''))  {
        $stmnt = DB::$connection->prepare($sql);
        $stmnt->execute($params);
        $filteredCount = $stmnt->rowCount();     
    }

    $results = array(
        "draw" => isset($_REQUEST['draw']) ? intval( $_REQUEST['draw'] ) : 0,        
        "recordsTotal" => intval($totalCount),
        "recordsFiltered" => intval($filteredCount),
        "data" => $data);

    DB::disconnect();     

    echo json_encode($results);
?>