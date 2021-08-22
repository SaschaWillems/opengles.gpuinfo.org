/** 		
 *
 * OpenGL ES hardware capability database MySQL database structure
 *
 * Copyright (C) 2011-2021 by Sascha Willems (www.saschawillems.de)
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

CREATE TABLE `binaryprogramformats` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME_UNIQUE` (`NAME`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `binaryshaderformats` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME_UNIQUE` (`NAME`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

CREATE TABLE `compressedformats` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `DISPLAYNAME` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`,`NAME`),
  UNIQUE KEY `NAME` (`NAME`),
  KEY `IName` (`NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=63818 DEFAULT CHARSET=utf8;

CREATE TABLE `devicefeatures` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DEVICEFEATURE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `DEVICEFEATURE` (`DEVICEFEATURE`)
) ENGINE=MyISAM AUTO_INCREMENT=2721 DEFAULT CHARSET=utf8;

CREATE TABLE `devicename_mapping` (
  `device` char(255) NOT NULL,
  `name` char(255) DEFAULT NULL,
  `note` char(255) DEFAULT NULL,
  PRIMARY KEY (`device`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Human readable device name	';

CREATE TABLE `egl_clientapis` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` char(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME_UNIQUE` (`NAME`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

CREATE TABLE `egl_extensions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME` (`NAME`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

CREATE TABLE `extensions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME` (`NAME`),
  KEY `IName` (`NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=78009 DEFAULT CHARSET=utf8;

CREATE TABLE `googledevicelist` (
  `retailbranding` varchar(255) CHARACTER SET latin1 NOT NULL,
  `marketingname` varchar(255) CHARACTER SET latin1 NOT NULL,
  `device` varchar(255) CHARACTER SET latin1 NOT NULL,
  `model` varchar(64) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`device`,`model`,`marketingname`,`retailbranding`),
  KEY `Device` (`device`),
  KEY `Model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `reports` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for this report',
  `DESCRIPTION` char(255) NOT NULL COMMENT 'Unique description to identify report',
  `SUBMITTER` tinytext NOT NULL,
  `SUBMISSIONDATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DEVICE` varchar(255) NOT NULL COMMENT 'Device name',
  `OS` tinytext NOT NULL COMMENT 'Android version',
  `SCREENWIDTH` int(11) NOT NULL COMMENT 'Width of screen in pixels',
  `SCREENHEIGHT` int(11) NOT NULL COMMENT 'Height of screen in pixels',
  `CPUCORES` int(11) DEFAULT NULL,
  `CPUSPEED` float DEFAULT NULL,
  `CPUARCH` varchar(45) DEFAULT NULL,
  `GL_VENDOR` tinytext NOT NULL,
  `GL_RENDERER` tinytext NOT NULL,
  `GL_VERSION` tinytext NOT NULL,
  `GL_SHADING_LANGUAGE_VERSION` tinytext NOT NULL,
  `EGL_VENDOR` tinytext NOT NULL,
  `EGL_VERSION` tinytext NOT NULL,
  `ESVERSION_MAJOR` int(11) DEFAULT NULL,
  `ESVERSION_MINOR` int(11) DEFAULT NULL,
  `SHADINGLANGUAGEVERSION_MAJOR` int(11) DEFAULT NULL,
  `SHADINGLANGUAGEVERSION_MINOR` int(11) DEFAULT NULL,
  `reportversion` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `DESCRIPTION` (`DESCRIPTION`),
  KEY `DEVICE` (`DEVICE`)
) ENGINE=MyISAM AUTO_INCREMENT=5504 DEFAULT CHARSET=utf8;

CREATE TABLE `reports_binaryprogramformats` (
  `REPORTID` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_binaryshaderformats` (
  `REPORTID` int(11) NOT NULL,
  `BINARYSHADERFORMATID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`BINARYSHADERFORMATID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_compressedformats` (
  `REPORTID` int(11) NOT NULL,
  `COMPRESSEDFORMATID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`COMPRESSEDFORMATID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_devicefeatures` (
  `REPORTID` int(11) NOT NULL,
  `DEVICEFEATUREID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`DEVICEFEATUREID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_eglclientapis` (
  `reportid` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`reportid`,`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_eglextensions` (
  `REPORTID` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_es20caps` (
  `REPORTID` int(11) NOT NULL,
  `GL_MAX_COMBINED_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_CUBE_MAP_TEXTURE_SIZE` int(11) DEFAULT '0',
  `GL_MAX_FRAGMENT_UNIFORM_VECTORS` int(11) DEFAULT '0',
  `GL_MAX_RENDERBUFFER_SIZE` int(11) DEFAULT '0',
  `GL_MAX_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_TEXTURE_SIZE` int(11) DEFAULT '0',
  `GL_MAX_VARYING_VECTORS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_ATTRIBS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_UNIFORM_VECTORS` int(11) DEFAULT '0',
  `GL_MAX_VIEWPORT_DIMS` int(11) DEFAULT '0',
  `GL_NUM_COMPRESSED_TEXTURE_FORMATS` int(11) DEFAULT '0',
  `GL_NUM_SHADER_BINARY_FORMATS` int(11) DEFAULT '0',
  `GL_NUM_PROGRAM_BINARY_FORMATS` int(11) DEFAULT '0',
  PRIMARY KEY (`REPORTID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_es30caps` (
  `REPORTID` int(11) NOT NULL,
  `GL_MAX_3D_TEXTURE_SIZE` int(11) DEFAULT '0',
  `GL_MAX_ARRAY_TEXTURE_LAYERS` int(11) DEFAULT '0',
  `GL_MAX_COLOR_ATTACHMENTS` int(11) DEFAULT '0',
  `GL_MAX_COMBINED_FRAGMENT_UNIFORM_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_COMBINED_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_COMBINED_UNIFORM_BLOCKS` int(11) DEFAULT '0',
  `GL_MAX_COMBINED_VERTEX_UNIFORM_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_CUBE_MAP_TEXTURE_SIZE` int(11) DEFAULT '0',
  `GL_MAX_DRAW_BUFFERS` int(11) DEFAULT '0',
  `GL_MAX_ELEMENT_INDEX` int(11) DEFAULT '0',
  `GL_MAX_ELEMENTS_INDICES` int(11) DEFAULT '0',
  `GL_MAX_ELEMENTS_VERTICES` int(11) DEFAULT '0',
  `GL_MAX_FRAGMENT_INPUT_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_FRAGMENT_UNIFORM_BLOCKS` int(11) DEFAULT '0',
  `GL_MAX_FRAGMENT_UNIFORM_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_FRAGMENT_UNIFORM_VECTORS` int(11) DEFAULT '0',
  `GL_MIN_PROGRAM_TEXEL_OFFSET` int(11) DEFAULT '0',
  `GL_MAX_PROGRAM_TEXEL_OFFSET` int(11) DEFAULT '0',
  `GL_MAX_RENDERBUFFER_SIZE` int(11) DEFAULT '0',
  `GL_MAX_SAMPLES` int(11) DEFAULT '0',
  `GL_MAX_SERVER_WAIT_TIMEOUT` int(11) DEFAULT '0',
  `GL_MAX_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_TEXTURE_LOD_BIAS` int(11) DEFAULT '0',
  `GL_MAX_TEXTURE_SIZE` int(11) DEFAULT '0',
  `GL_MAX_TRANSFORM_FEEDBACK_INTERLEAVED_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_TRANSFORM_FEEDBACK_SEPARATE_ATTRIBS` int(11) DEFAULT '0',
  `GL_MAX_TRANSFORM_FEEDBACK_SEPARATE_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_UNIFORM_BLOCK_SIZE` int(11) DEFAULT '0',
  `GL_MAX_UNIFORM_BUFFER_BINDINGS` int(11) DEFAULT '0',
  `GL_MAX_VARYING_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_VARYING_VECTORS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_ATTRIBS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_TEXTURE_IMAGE_UNITS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_OUTPUT_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_UNIFORM_BLOCKS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_UNIFORM_COMPONENTS` int(11) DEFAULT '0',
  `GL_MAX_VERTEX_UNIFORM_VECTORS` int(11) DEFAULT '0',
  `GL_MAX_VIEWPORT_DIMS` int(11) DEFAULT '0',
  PRIMARY KEY (`REPORTID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_es31caps` (
  `REPORTID` int(11) NOT NULL,
  `GL_MAX_ATOMIC_COUNTER_BUFFER_BINDINGS` int(11) DEFAULT NULL,
  `GL_MAX_ATOMIC_COUNTER_BUFFER_SIZE` int(11) DEFAULT NULL,
  `GL_MAX_COLOR_TEXTURE_SAMPLES` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_COMPUTE_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_SHADER_OUTPUT_RESOURCES` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_SHARED_MEMORY_SIZE` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_TEXTURE_IMAGE_UNITS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_UNIFORM_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_COUNT[0]` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_COUNT[1]` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_COUNT[2]` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_INVOCATIONS` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_SIZE[0]` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_SIZE[1]` int(11) DEFAULT NULL,
  `GL_MAX_COMPUTE_WORK_GROUP_SIZE[2]` int(11) DEFAULT NULL,
  `GL_MAX_DEPTH_TEXTURE_SAMPLES` int(11) DEFAULT NULL,
  `GL_MAX_FRAGMENT_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_FRAGMENT_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_FRAGMENT_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_FRAGMENT_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_FRAMEBUFFER_HEIGHT` int(11) DEFAULT NULL,
  `GL_MAX_FRAMEBUFFER_SAMPLES` int(11) DEFAULT NULL,
  `GL_MAX_FRAMEBUFFER_WIDTH` int(11) DEFAULT NULL,
  `GL_MAX_IMAGE_UNITS` int(11) DEFAULT NULL,
  `GL_MAX_INTEGER_SAMPLES` int(11) DEFAULT NULL,
  `GL_MIN_PROGRAM_TEXTURE_GATHER_OFFSET` int(11) DEFAULT NULL,
  `GL_MAX_PROGRAM_TEXTURE_GATHER_OFFSET` int(11) DEFAULT NULL,
  `GL_MAX_SAMPLE_MASK_WORDS` int(11) DEFAULT NULL,
  `GL_MAX_SHADER_STORAGE_BLOCK_SIZE` int(11) DEFAULT NULL,
  `GL_MAX_SHADER_STORAGE_BUFFER_BINDINGS` int(11) DEFAULT NULL,
  `GL_MAX_UNIFORM_LOCATIONS` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_ATTRIB_BINDINGS` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_ATTRIB_RELATIVE_OFFSET` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_ATTRIB_STRIDE` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_VERTEX_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  PRIMARY KEY (`REPORTID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_es32caps` (
  `REPORTID` int(11) NOT NULL,
  `GL_MIN_SAMPLE_SHADING_VALUE` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_GEOMETRY_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_TESS_CONTROL_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_COMBINED_TESS_EVALUATION_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_DEBUG_GROUP_STACK_DEPTH` int(11) DEFAULT NULL,
  `GL_MAX_DEBUG_LOGGED_MESSAGES` int(11) DEFAULT NULL,
  `GL_MAX_DEBUG_MESSAGE_LENGTH` int(11) DEFAULT NULL,
  `GL_MIN_FRAGMENT_INTERPOLATION_OFFSET` int(11) DEFAULT NULL,
  `GL_MAX_FRAGMENT_INTERPOLATION_OFFSET` int(11) DEFAULT NULL,
  `GL_MAX_FRAMEBUFFER_LAYERS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_INPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_OUTPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_OUTPUT_VERTICES` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_SHADER_INVOCATIONS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_TEXTURE_IMAGE_UNITS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_TOTAL_OUTPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_UNIFORM_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_GEOMETRY_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_LABEL_LENGTH` int(11) DEFAULT NULL,
  `GL_MAX_PATCH_VERTICES` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_INPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_OUTPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_TEXTURE_IMAGE_UNITS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_TOTAL_OUTPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_UNIFORM_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_CONTROL_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_ATOMIC_COUNTERS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_ATOMIC_COUNTER_BUFFERS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_IMAGE_UNIFORMS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_INPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_OUTPUT_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_SHADER_STORAGE_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_TEXTURE_IMAGE_UNITS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_UNIFORM_BLOCKS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_EVALUATION_UNIFORM_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TESS_GEN_LEVEL` int(11) DEFAULT NULL,
  `GL_MAX_TESS_PATCH_COMPONENTS` int(11) DEFAULT NULL,
  `GL_MAX_TEXTURE_BUFFER_SIZE` int(11) DEFAULT NULL,
  PRIMARY KEY (`REPORTID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_extensions` (
  `REPORTID` int(11) NOT NULL,
  `EXTENSIONID` int(11) NOT NULL,
  PRIMARY KEY (`REPORTID`,`EXTENSIONID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `reports_sensors` (
  `REPORTID` int(11) NOT NULL,
  `SENSORID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `MAXRANGE` float NOT NULL,
  `RESOLUTION` float NOT NULL,
  PRIMARY KEY (`NAME`,`REPORTID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE ALGORITHM=UNDEFINED DEFINER=`DATABASE_NAME`@`%` SQL SECURITY DEFINER VIEW `viewCompressedFormats` AS select if(isnull(`fmt`.`DISPLAYNAME`),`fmt`.`NAME`,`fmt`.`DISPLAYNAME`) AS `name`,(select count(0) from ((`reports` join `reports_compressedformats` on((`reports_compressedformats`.`REPORTID` = `reports`.`ID`))) join `compressedformats` on((`reports_compressedformats`.`COMPRESSEDFORMATID` = `compressedformats`.`ID`))) where (((`compressedformats`.`NAME` = `fmt`.`NAME`) / (select count(`reports`.`DESCRIPTION`) from `reports`)) * 100)) AS `coverage` from `compressedformats` `fmt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`DATABASE_NAME`@`%` SQL SECURITY DEFINER VIEW `viewDeviceFeatures` AS select `df`.`DEVICEFEATURE` AS `name`,(select count(0) from ((`reports` join `reports_devicefeatures` on((`reports_devicefeatures`.`REPORTID` = `reports`.`ID`))) join `devicefeatures` on((`devicefeatures`.`ID` = `reports_devicefeatures`.`DEVICEFEATUREID`))) where (`devicefeatures`.`DEVICEFEATURE` = `df`.`DEVICEFEATURE`)) AS `reports` from `devicefeatures` `df`;

CREATE ALGORITHM=UNDEFINED DEFINER=`DATABASE_NAME`@`%` SQL SECURITY DEFINER VIEW `viewEGLExtensions` AS select `ext`.`NAME` AS `name`,(select count(0) from ((`reports` join `reports_eglextensions` on((`reports_eglextensions`.`REPORTID` = `reports`.`ID`))) join `egl_extensions` on((`egl_extensions`.`ID` = `reports_eglextensions`.`ID`))) where (`egl_extensions`.`NAME` = `ext`.`NAME`)) AS `reports` from `egl_extensions` `ext`;

CREATE ALGORITHM=UNDEFINED DEFINER=`DATABASE_NAME`@`%` SQL SECURITY DEFINER VIEW `viewExtensions` AS select `ext`.`NAME` AS `name`,(select count(0) from ((`reports` join `reports_extensions` on((`reports_extensions`.`REPORTID` = `reports`.`ID`))) join `extensions` on((`extensions`.`ID` = `reports_extensions`.`EXTENSIONID`))) where (`extensions`.`NAME` = `ext`.`NAME`)) AS `reports` from `extensions` `ext` where (`ext`.`NAME` <> '');

DELIMITER $$
CREATE DEFINER=`DATABASE_NAME`@`%` PROCEDURE `import_glesreport`(IN `xmlstring` TEXT CHARSET utf8)
    NO SQL
BEGIN       
	DECLARE rowCount INT unsigned;
	DECLARE rowIndex INT unsigned;
	DECLARE reportID INT unsigned;
	DECLARE fieldName varchar(255);
	SET @xml = xmlstring;

	
	INSERT IGNORE INTO reports 
		(Submitter, Description, reportversion,
		 Device, OS, Screenwidth, Screenheight, CPUCores, CPUSpeed, CPUArch,
		 GL_VENDOR, GL_RENDERER, GL_VERSION, GL_SHADING_LANGUAGE_VERSION,
		 EGL_VENDOR, EGL_VERSION,
		 ESVERSION_MAJOR, ESVERSION_MINOR,
		 SHADINGLANGUAGEVERSION_MAJOR, SHADINGLANGUAGEVERSION_MINOR)
	VALUES	
		(
		ExtractValue(@xml, '/report/@submitter'),
		ExtractValue(@xml, '/report/@description'),
		ExtractValue(@xml, '/report/@reportversion'),
		
		ExtractValue(@xml, '/report/device/system/devicename'),
		ExtractValue(@xml, '/report/device/system/os'),
		ExtractValue(@xml, '/report/device/system/screenwidth'),
		ExtractValue(@xml, '/report/device/system/screenheight'),
		ExtractValue(@xml, '/report/device/system/cpucores'),
		ExtractValue(@xml, '/report/device/system/cpuspeed'),
		ExtractValue(@xml, '/report/device/system/cpuarch'),
		
		ExtractValue(@xml, '/report/opengles/implementation/vendor'),		
		ExtractValue(@xml, '/report/opengles/implementation/renderer'),		
		ExtractValue(@xml, '/report/opengles/implementation/version'),		
		ExtractValue(@xml, '/report/opengles/implementation/shadinglanguageversion'),

		ExtractValue(@xml, '/report/egl/implementation/vendor'),
		ExtractValue(@xml, '/report/egl/implementation/version'),

		ExtractValue(@xml, '/report/opengles/implementation/majorversion'),
		ExtractValue(@xml, '/report/opengles/implementation/minorversion'),

		ExtractValue(@xml, '/report/opengles/implementation/shadinglanguagemajorversion'),
		ExtractValue(@xml, '/report/opengles/implementation/shadinglanguageminorversion')
		);

	SELECT max(ID) from reports into reportID;
	
	
    INSERT IGNORE INTO reports_es20caps (REPORTID) VALUES (reportID);
	SET rowCount = extractValue(@xml,'count(/report/opengles/es20caps/cap)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		
		set @query = CONCAT('UPDATE reports_es20caps SET @column = @value WHERE ReportID = ', CAST(ReportID as CHAR(8)));
		set @query = replace(@query, '@column', extractValue(@xml, CONCAT('/report/opengles/es20caps/cap[', rowIndex, ']/@name')));
		set @query = replace(@query, '@value', extractValue(@xml, CONCAT('/report/opengles/es20caps/cap[', rowIndex, ']')));
		IF (extractValue(@xml, CONCAT('/report/opengles/es20caps/cap[', rowIndex, ']')) != 'unknown') THEN
			PREPARE updateStatement FROM @query;
			EXECUTE updateStatement;
		END IF;
	END WHILE;

	
    INSERT IGNORE INTO reports_es30caps (REPORTID) VALUES (reportID);
	SET rowCount = extractValue(@xml,'count(/report/opengles/es30caps/cap)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		
		set @query = CONCAT('UPDATE reports_es30caps SET @column = @value WHERE ReportID = ', CAST(ReportID as CHAR(8)));
		set @query = replace(@query, '@column', extractValue(@xml, CONCAT('/report/opengles/es30caps/cap[', rowIndex, ']/@name')));
		set @query = replace(@query, '@value', extractValue(@xml, CONCAT('/report/opengles/es30caps/cap[', rowIndex, ']')));
		IF (extractValue(@xml, CONCAT('/report/opengles/es30caps/cap[', rowIndex, ']')) != 'unknown') THEN
			PREPARE updateStatement FROM @query;
			EXECUTE updateStatement;
		END IF;
	END WHILE;
    
    
    INSERT IGNORE INTO reports_es31caps (REPORTID) VALUES (reportID);
	SET rowCount = extractValue(@xml,'count(/report/opengles/es31caps/cap)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		
		set @query = CONCAT('UPDATE reports_es31caps SET `@column` = @value WHERE ReportID = ', CAST(ReportID as CHAR(8)));
		set @query = replace(@query, '@column', extractValue(@xml, CONCAT('/report/opengles/es31caps/cap[', rowIndex, ']/@name')));
		set @query = replace(@query, '@value', extractValue(@xml, CONCAT('/report/opengles/es31caps/cap[', rowIndex, ']')));
		IF (extractValue(@xml, CONCAT('/report/opengles/es31caps/cap[', rowIndex, ']')) != 'unknown') THEN
			PREPARE updateStatement FROM @query;
			EXECUTE updateStatement;
		END IF;
	END WHILE;    


    INSERT IGNORE INTO reports_es32caps (REPORTID) VALUES (reportID);
	SET rowCount = extractValue(@xml,'count(/report/opengles/es32caps/cap)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		
		set @query = CONCAT('UPDATE reports_es32caps SET `@column` = @value WHERE ReportID = ', CAST(ReportID as CHAR(8)));
		set @query = replace(@query, '@column', extractValue(@xml, CONCAT('/report/opengles/es32caps/cap[', rowIndex, ']/@name')));
		set @query = replace(@query, '@value', extractValue(@xml, CONCAT('/report/opengles/es32caps/cap[', rowIndex, ']')));
		IF (extractValue(@xml, CONCAT('/report/opengles/es32caps/cap[', rowIndex, ']')) != 'unknown') THEN
			PREPARE updateStatement FROM @query;
			EXECUTE updateStatement;
		END IF;
	END WHILE;    
	
	SET rowCount = extractValue(@xml,'count(/report/device/features/feature)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO devicefeatures (devicefeature) VALUES (ExtractValue(@xml, CONCAT('/report/device/features/feature[', rowIndex, ']')));
		
		INSERT IGNORE INTO reports_devicefeatures
			(ReportID, DeviceFeatureID) 
		VALUES 
			(reportID, (select ID from devicefeatures where devicefeature =  ExtractValue(@xml, CONCAT('/report/device/features/feature[', rowIndex, ']'))) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/device/sensors/sensor)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO reports_sensors 
			(reportid, sensorid, name, maxrange, resolution) 
		VALUES 
			(
			reportID,
			rowIndex,
			ExtractValue(@xml, CONCAT('/report/device/sensors/sensor[', rowIndex, ']')),
			ExtractValue(@xml, CONCAT('/report/device/sensors/sensor[', rowIndex, ']/@maxrange')),
			ExtractValue(@xml, CONCAT('/report/device/sensors/sensor[', rowIndex, ']/@resolution'))
			);
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/opengles/extensions/extension)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO extensions (name) VALUES (ExtractValue(@xml, CONCAT('/report/opengles/extensions/extension[', rowIndex, ']')));
		
		INSERT IGNORE INTO reports_extensions 
			(ReportID, ExtensionID) 
		VALUES 
			(reportID, (select ID from extensions where Name =  ExtractValue(@xml, CONCAT('/report/opengles/extensions/extension[', rowIndex, ']'))) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/opengles/compressedformats/compressedformat)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO compressedformats (name) VALUES (ExtractValue(@xml, CONCAT('/report/opengles/compressedformats/compressedformat[', rowIndex, ']')));
		INSERT IGNORE INTO reports_compressedformats
			(ReportID, CompressedFormatID) 
		VALUES 
			(reportID, (select ID from compressedformats where Name =  ExtractValue(@xml, CONCAT('/report/opengles/compressedformats/compressedformat[', rowIndex, ']')) limit 1) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/opengles/binaryshaderformats/binaryshaderformat)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO binaryshaderformats (name) VALUES (ExtractValue(@xml, CONCAT('/report/opengles/binaryshaderformats/binaryshaderformat[', rowIndex, ']')));
		INSERT IGNORE INTO reports_binaryshaderformats
			(ReportID, BinaryShaderFormatID) 
		VALUES 
			(reportID, (select ID from binaryshaderformats where Name =  ExtractValue(@xml, CONCAT('/report/opengles/binaryshaderformats/binaryshaderformat[', rowIndex, ']'))) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/opengles/binaryprogramformats/binaryprogramformat)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO binaryprogramformats (name) VALUES (ExtractValue(@xml, CONCAT('/report/opengles/binaryprogramformats/binaryprogramformat[', rowIndex, ']')));
		INSERT IGNORE INTO reports_binaryprogramformats
			(ReportID, ID) 
		VALUES 
			(reportID, (select ID from binaryprogramformats where Name =  ExtractValue(@xml, CONCAT('/report/opengles/binaryprogramformats/binaryprogramformat[', rowIndex, ']'))) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/egl/extensions/extension)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO egl_extensions (name) VALUES (ExtractValue(@xml, CONCAT('/report/egl/extensions/extension[', rowIndex, ']')));
		INSERT IGNORE INTO reports_eglextensions
			(ReportID, ID) 
		VALUES 
			(reportID, (select ID from egl_extensions where Name =  ExtractValue(@xml, CONCAT('/report/egl/extensions/extension[', rowIndex, ']'))) );
	END WHILE;

	
	SET rowCount = extractValue(@xml,'count(/report/egl/clientapis/clientapi)');
	SET rowIndex = 0;
	WHILE rowIndex < rowCount do        
		SET rowIndex := rowIndex + 1;
		INSERT IGNORE INTO egl_clientapis (name) VALUES (ExtractValue(@xml, CONCAT('/report/egl/clientapis/clientapi[', rowIndex, ']')));
		INSERT IGNORE INTO reports_eglclientapis
			(ReportID, ID) 
		VALUES 
			(reportID, (select ID from egl_clientapis where Name =  ExtractValue(@xml, CONCAT('/report/egl/clientapis/clientapi[', rowIndex, ']'))) );
	END WHILE;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`DATABASE_NAME`@`%` FUNCTION `check_glesreport`(`xmlstring` TEXT CHARSET utf8) RETURNS char(255) CHARSET latin1
BEGIN
	DECLARE reportCount int;

	SET @xml = xmlstring;
	SET @result = 'new';
	SET @description = ExtractValue(@xml, '/report/@description');

	SELECT count(ID) from reports where description = @description into reportCount;	

	IF (reportCount > 0) THEN
		SET @result = CONCAT('duplicate|', @description);
	END IF;
	
	RETURN @result;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`DATABASE_NAME`@`%` FUNCTION `devicename`(`indevice` char(255) CHARSET utf8) RETURNS char(255) CHARSET utf8
BEGIN
	DECLARE devicename char(255);
	SELECT name from devicename_mapping where device = trim(indevice) into devicename;
	
	IF (devicename != '') THEN
		RETURN CONCAT(devicename, ' (', indevice, ')');
	ELSE
		RETURN indevice;
	END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`DATABASE_NAME`@`%` FUNCTION `devicename_short`(`indevice` char(255) CHARSET utf8) RETURNS char(255) CHARSET utf8
BEGIN
	DECLARE devicename char(255);
	SELECT name from devicename_mapping where device = trim(indevice) into devicename;
	
	IF (devicename != '') THEN
		RETURN devicename;
	ELSE
		RETURN indevice;
	END IF;

END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`DATABASE_NAME`@`%` FUNCTION `formatname`(`informat` char(255) CHARSET utf8) RETURNS char(255) CHARSET utf8
BEGIN
	DECLARE formatname char(255);
    
    IF (INSTR(informat, '0x') > 0) THEN
		return CONCAT('unknown (', informat, ')');
    END IF;
    
    if (INSTR(informat, 'GL_') > 0) THEN
		RETURN informat;
    ELSE
		RETURN CONCAT('GL_', informat);
    END IF;
END$$
DELIMITER ;
