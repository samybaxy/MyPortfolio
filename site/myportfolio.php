<?php
/**
 * MyPortfolio Site entry point
 *
 * @package	      MyPortfolio.Site
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

// no direct access
defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('Myportfolio');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();