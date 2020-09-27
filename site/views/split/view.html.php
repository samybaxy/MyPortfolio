<?php
/**
 * MyPortfolio Split Site View
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

class MyPortfolioViewSplit extends JViewLegacy {
    protected $state;
    protected $data;
    protected $pagination;
    protected $params;
    protected $projects;
    protected $view;

    /**
    * Method to display the view
    *
    * @param string  $tpl  The name of the template file to parse; automatically searches through the template paths.
    *
    * @return  mixed   A string if successful, otherwise an Error object.
    * @since 1.6
    */
    function display( $tpl = null ) {
        //get Component wide parameters
        $this->params   = JComponentHelper::getParams('com_myportfolio');
        $this->data     = $this->get('CatProjects', 'Myportfolio');
        $this->view     = JFactory::getApplication()->input->get('view', '', 'WORD');

        if($this->params->get('fullscreen')) {
            JFactory::getApplication()->input->set('tmpl', 'component');
        }

        // Display the view
        $this->_prepareDocument();
        parent::display( $tpl );
    }

    protected function _prepareDocument() {
        $app	= JFactory::getApplication();
        $menus	= $app->getMenu();
        $title	= null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else {
            $this->params->def('page_heading', JText::_('COM_MYPORTFOLIO_DEFAULT_PAGE_TITLE'));
        }

        // Set metadata for all tags menu item
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

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}