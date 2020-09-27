<?php
/**
 * MyPortfolio Administrator Category controller
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;

/**
 * myPortfolio controller edit controller class.
 *
 */

class MyportfolioControllerCategory extends JControllerForm	{

    protected	$option 		= 'com_myportfolio';

    function __construct($config=array()) {
        parent::__construct($config);
    }

    protected function allowAdd($data = array()) {
        // Initialise variables.
        $user = JFactory::getUser();
        $allow		= null;
        $allow	= $user->authorise('core.create', 'com_myportfolio');
        if ($allow === null) {
            return parent::allowAdd($data);
        } else {
            return $allow;
        }
    }

    /**
     * Method to check if you can add a new record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id') {
        $user		= JFactory::getUser();
        $allow		= null;
        $allow	= $user->authorise('core.edit', 'com_myportfolio');
        if ($allow === null) {
            return parent::allowEdit($data, $key);
        } else {
            return $allow;
        }
    }
}