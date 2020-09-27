<?php
/*
 * @package		myPortfolio.Helper
 * @copyright	Copyright (C) 2010 - 2012 www.samybaxy.net, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Myportfolio Component
 * @license		GNU/GPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class PortfolioHelper {
		static function parseXMLTemplateFiles($tBaseXmlDir) {
			// Read the template folder to find templates
			jimport('joomla.filesystem.folder');
			$templateDirs = JFolder::folders($tBaseXmlDir);
	
			$rows = array();
	
			// Check that the directory contains an xml file
			foreach ($templateDirs as $templateDir)
			{
				if(!$data = PortfolioHelper::parseXMLTemplateFile($tBaseXmlDir, $templateDir)) {
					continue;
				} else {
					$rows[] = $data;
				}
			}
	
			return $rows;
		}
		
		static function parseXMLTemplateFile($tBaseXmlDir, $templateDir) {
			// Check of the xml file exists
			if(!is_file($tBaseXmlDir.'/'.$templateDir.'/'.'template.xml')) {
				return false;
			}
			
			$xml = JInstaller::parseXMLInstallFile($tBaseXmlDir.'/'.$templateDir.'/'.'template.xml');
			
			if ($xml['type'] != 'template') {
				return false;
			}
	
			$data = new StdClass();
			$data->directory = $templateDir;
	
			foreach($xml as $key => $value) {
				$data->$key = $value;
			}

			$data->tName = JString::strtolower(str_replace(' ', '_', $data->name));
	
			return $data;
		}
	}