<?php
/**
 * MyPortfolio Site Controller
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

class MyportfolioController extends JControllerLegacy {
    //function to register task
    function __construct( $config = array() ) {
        parent::__construct( $config );
        $this->registerTask( 'featProj', 'featProj' );
        $this->registerTask( 'project', 'project' );
    }

    /**
     * Proxy for getModel.
     * @param string, suffix for model class
     * @param string, prefix for model class
     * @param array
     *
     * @since	1.6
     *
     * @return model object
     */
    public function getModel($name = 'Myportfolio', $prefix = 'MyportfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Override Method to display a view.
     *
     * @param boolean If true, the view output will be cached
     * @param boolean An array of safe url parameters and their
     * variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return JController This object to support chaining.
     * @since 2.5
     */
    public function display($cachable = false, $urlparams = false) {
        // use the view display method
        $input = JFactory::getApplication()->input;

        //load model
        $model = $this->getModel( 'Myportfolio' );

        // Set the default view name and format from the Request.
        $vName = $this->input->get('view');
        $this->input->set('view', $vName);

        // Set the view and the model
        $view = $this->getView($vName, 'html');
        $view->setModel( $model, true );

        parent::display();
    }

    //public function, gets all projects in category
    function featProj() {
        //load model
        $model = $this->getModel( 'Myportfolio' );

        // Set the view and the model
        $input = JFactory::getApplication()->input;
        $view = $input->get( 'view' );

        //Set the view and the model
        $view = $this->getView( $view, 'html' );
        $view->setModel( $model, true );

        // Display the Standard frontend template
        $view->ajaxLoad();
    }

    //individual project ajax task
    function project() {
        $input = JFactory::getApplication()->input;

        //load model
        $model = $this->getModel( 'myportfolio' );

        // Set the view and the model
        $view = $input->get('view');

        // Set the view and the model
        $view = $this->getView($view, 'html');
        $view->setModel( $model, true );

        // Display the Standard frontend template
        $view->ajaxLoadProject();
    }
}