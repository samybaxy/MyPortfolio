<?php
/**
 * MyPortfolio Administrator entry point
 * 
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

	// no direct access
	defined('_JEXEC') or die;
	
	// Access check
	if (!JFactory::getUser()->authorise('core.manage', 'com_myportfolio')) {
        throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
	}

	$controller = JControllerLegacy::getInstance('Myportfolio');
	$controller->execute(JFactory::getApplication()->input->get('task'));
	$controller->redirect();