<?php
/**
 * @package		MyPortfolio.Helper
 * @copyright	Copyright (C) 2010 - 2018 www.samybaxy.net, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component MyPortfolio Component
 * @license		GNU/GPLv3
 */
defined('_JEXEC') or die;

class MyportfolioHelper {
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     *
     * @since   1.6
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_MYPORTFOLIO_CATEGORYS'),
            'index.php?option=com_myportfolio&view=categories',
            $vName == 'categories'
        );
    }

    public static function addImageSubmenu($vName)
    {
        $jinput         = JFactory::getApplication()->input;
        $catid          = $jinput->get('catid', null, 'int');

        JHtmlSidebar::addEntry(
            JText::_('COM_MYPORTFOLIO_MANAGE_PROJECTS'),
            'index.php?option=com_myportfolio&view=projects&catid='.$catid,
            $vName == 'projects'
        );
    }
}