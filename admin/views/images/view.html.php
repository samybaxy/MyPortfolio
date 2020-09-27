<?php
/**
 * MyPortfolio Administrator Images view
 * 
 * @package		MyPortfolio.Administrator
 * @subpackage	com_myportfolio
 * @author		samybaxy
 * @copyright   Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link		https://www.samybaxy.net
 * @license		GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

//import Joomla folder system
jimport('joomla.filesystem.folder');

class MyportfolioViewImages extends JViewLegacy {

    protected $cat;
    protected $project;
    protected $imgDirectory;
    protected $session;
    protected $canDo;

    function display( $tpl = null ) {
        $config = JComponentHelper::getParams('com_media');

        //Initialize reusable variables
        $session        = JFactory::getSession();
        $this->canDo    = JHelperContent::getActions('com_myportfolio');
        $this->session  = $session;
        $this->config   = &$config;
        $jinput         = JFactory::getApplication()->input;
        $catid          = $jinput->get('catid', null, 'int');
        $pid            = $jinput->get('pid', null, 'int');

        MyportfolioHelper::addImageSubmenu('projects');

        $model = $this->getModel($name = 'Images', $prefix = 'CategoriesModel');
        $cat_project_name = $model->getCatProjectName($catid, $pid);

        //set session variable to be used for image upload path
        $this->cat = $cat_project_name[0];
        $this->project = $cat_project_name[1];
        $pathToFiles = $this->cat.DS.$this->project;

        //We need to check if a filepath exists if not, create it.
        $this->imgDirectory = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->cat.DS.$this->project;
        if(!JFolder::exists($this->imgDirectory)) {
            JFolder::create($this->imgDirectory);
        }

        $session = JFactory::getSession();
        $session->set('pid', $pid);
        $session->set('pathToFiles', $pathToFiles);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        $jinput->set('hidemainmenu', true);
        $this->addToolbar($catid, $pid);
        $this->sidebar = JHtmlSidebar::render();
        parent::display( $tpl );
    }

    protected function addToolbar($catid, $pid) {
        $canDo  = $this->canDo;
        $bar    = JToolbar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_MYPORTFOLIO_IMG_UPLOAD'), 'palette');

        if ($canDo->get('core.delete')) {
            // Instantiate a new JLayoutFile instance and render the layout
            $layout = new JLayoutFile('toolbar.deletemedia');
            $bar->appendButton('Custom', $layout->render(array()), 'delete');

            JToolBarHelper::divider();
            JToolBarHelper::cancel('images.cancel', 'JTOOLBAR_CLOSE');
        }

        JHtmlSidebar::setAction('index.php?option=com_myportfolio&view=images&catid='.$catid.'&pid='.$pid);
    }
}