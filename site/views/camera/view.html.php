<?php
/**
 * MyPortfolio Camera View
 *
 * @package	      MyPortfolio.Site
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

	// No direct access
	defined( '_JEXEC' ) or die;
	
	// load jpath root	
	$jPath = JUri::base();
	
	class MyPortfolioViewCamera extends JViewLegacy {

		protected $data;
		protected $pList;
		protected $params;

		function display( $tpl = null ) {
			//get Component wide parameters
			$params = JComponentHelper::getParams('com_myportfolio');

			$this->data		= $this->get('Portfolio', 'Myportfolio');
			$this->pList	= $this->get('CatProjects', 'Myportfolio');
			$this->params   = $params;
			
			$this->_prepareDocument();
			parent::display($tpl);
		}

		protected function _prepareDocument() {			
			$app	= JFactory::getApplication();
			$menus	= $app->getMenu();
			$title	= null;
			
			// Because the application sets a default page title,
			// we need to get it from the menu item itself
			$menu = $menus->getActive();
			if($menu) {
				$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
			} 

			$title = $this->params->get('page_title', '');
			
			if (empty($title)) {
				$title = $app->getCfg('sitename');
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			
			$this->document->setTitle($title);
			
			if ($this->params->get('menu-meta_description'))
			{
				$this->document->setDescription($this->params->get('menu-meta_description'));
			}
			
			if ($this->params->get('menu-meta_keywords'))
			{
				$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
			}
			
			if ($this->params->get('robots'))
			{
				$this->document->setMetadata('robots', $this->params->get('robots'));
			}
		}

		// view called for the project task
		function ajaxLoadProject() {
			//get Component wide parameters
			$params = JComponentHelper::getParams('com_myportfolio');
			$this->params   = $params;
			$this->data     = $this->get('Project', 'Myportfolio');
			
			//Set Layout and Load Template
			$this->setLayout('async');
			$this->loadTemplate();
			parent::display($tpl = null);
		}
	}