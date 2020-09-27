<?php
/**
 * myPortfolio Administrator loader
 * 
 * @package	      myPortfolio
 * @subpackage	  components
 * @author		  samybaxy
 * @link          http://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

// no direct access
	defined('_JEXEC') or die('Restricted access');

if (!class_exists('JLoader')) {
    require_once( JPATH_LIBRARIES.DS.'loader.php');
}

	class myportfolioLoader extends JLoader {
		function import( $filePath, $base = null, $key = 'libraries.' ) {
			static $paths;
	
			if (!isset($paths)) {
				$paths = array();
			}
	
			$keyPath = $key ? $key . $filePath : $filePath;			
	
			if (!isset($paths[$keyPath])) {
				if ( ! $base ) {
					$base =  JPATH_ADMINISTRATOR.DS.'components'.DS.'com_myportfolio'.DS.'libraries';
				}
	
				$parts = explode( '.', $filePath );	
				$classname = array_pop( $parts );				
				
				switch($classname) {
					case 'helper' :
						$classname = ucfirst(array_pop( $parts )).ucfirst($classname);
						break;
	
					default :
						$classname = ucfirst($classname);
						break;
				}
	
				$path  = str_replace( '.', DS, $filePath );				
	
				if (strpos($filePath, 'myportfolio') === 0)	{
					$classname	= 'myportfolio'.$classname;
					$classes	= JLoader::register($classname, $base.DS.$path.'.php');
					$rs			= isset($classes[strtolower($classname)]);
				}
				else {
					/*
					 * If it is not in the joomla namespace then we have no idea if
					 * it uses our pattern for class names/files so just include.
					 */
					$rs   = include($base.DS.$path.'.php');
				}
	
				$paths[$keyPath] = $rs;
			}
	
			return $paths[$keyPath];
		}
	}

	function myportfolioimport($path) {
		return myportfolioLoader::import($path);
	}