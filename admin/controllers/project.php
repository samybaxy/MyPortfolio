<?php
/**
 * MyPortfolio Administrator Project controller
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;
	
class MyportfolioControllerProject extends JControllerForm	{

    /**
     * @var    string  The prefix to use with controller messages.
     * @since  1.6
     */
    protected $text_prefix = 'COM_MYPORTFOLIO_PROJECT';

    /**
     * Method to check if you can edit a new record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id') {
        $user	= JFactory::getUser();
        $allow	= null;
        $allow	= $user->authorise('core.edit', 'com_myportfolio');
        if ($allow === null) {
            return parent::allowEdit($data, $key);
        } else {
            return $allow;
        }
    }

    /**
     * An Override method
     * Gets the URL arguments to append to a list redirect.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToListAppend()
    {
        $jinput = JFactory::getApplication()->input;
        $catid  = $jinput->get('catid', null, 'int');

        //get cat id from request
        if(empty($catid)) {
            $jform = $jinput->post->get('jform', 'array', 'array');
            $catid = $jform['catid'];
        }
        elseif (empty($catid)) {
            $session = JFactory::getSession();
            $catid = $session->get('catid');
        }

        $append = '&catid='.(int)$catid;

        // Setup redirect info.
        if ($tmpl = $this->input->get('tmpl', '', 'string'))
        {
            $append .= '&tmpl=' . $tmpl;
        }

        if ($forcedLanguage = $this->input->get('forcedLanguage', '', 'cmd'))
        {
            $append .= '&forcedLanguage=' . $forcedLanguage;
        }

        return $append;
    }
}