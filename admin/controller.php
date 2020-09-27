<?php
/**
 * MyPortfolio Administrator base controller
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

class MyportfolioController extends JControllerLegacy {
    function __construct() {
        parent::__construct();
        // Register Extra tasks
        $this->registerTask( 'upload' ,  'upload');
    }

    public function display($cachable = false, $urlparams = false) {

        require_once JPATH_COMPONENT.'/helpers/myportfolio.php';
        $view = $this->input->get('view', 'myportfolio');

        //@todo build cpanel instead of category redirect
        if($view == 'myportfolio') {
            $this->input->set('view', 'categories');
            $this->setRedirect(JRoute::_('index.php?&option=com_myportfolio&view=categories', false));
        }

        $layout 	= $this->input->get('layout', 'default');
        $id			= $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'category' && $layout == 'edit' && !$this->checkEditId('com_myportfolio.edit.category', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_myportfolio', false));

            return false;
        }

        // Check for edit form.
        if ($view == 'project' && $layout == 'edit' && !$this->checkEditId('com_myportfolio.edit.project', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_myportfolio&view=projects', false));

            return false;
        }

        // use the view display method
        parent::display();

        return $this;
    }
}