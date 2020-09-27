<?php
/**
 * MyPortfolio Reel View
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
	$jPath = JUri::root();
	
	class MyPortfolioViewReel extends JViewLegacy {
        protected $params;
        protected $page;
        protected $data;

		function display( $tpl = null ) {
			//get component params
			$params = JComponentHelper::getParams('com_myportfolio');			
			$this->params = $params;

			$input      = JFactory::getApplication()->input;
			$page       = $input->get('page', '1', 'INT');

			$this->data = $this->get('ProjectsPaginate', 'Myportfolio');
			$this->page = $page;

			// Display the view
			parent::display( $tpl );
		}
		
		// view called for a single Category task
		function ajaxLoad() {			
			//get component params
			$params         = JComponentHelper::getParams('com_myportfolio');
			$this->params   = $params;
			$input          = JFactory::getApplication()->input;

			$this->page     = $input->get('page', '1', 'INT');
			$this->data     = $this->get('ProjectsPaginate', 'Myportfolio');
			
			//Set Layout and Load Template
			$this->setLayout('async');
			$this->loadTemplate();
			parent::display($tpl = null);
		}
	}