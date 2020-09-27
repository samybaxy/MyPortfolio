<?php
/**
 * MyPortfolio Administrator Projects view
 * 
 * @package		MyPortfolio.Administrator
 * @subpackage	com_myportfolio
 * @author      samybaxy
 * @copyright   Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link		https://www.samybaxy.net
 * @license		GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;

class MyportfolioViewProjects extends JViewLegacy {
    /**
     * portfolio(s) view display method
     *
     * @return
     */

    protected $items;
    protected $pagination;
    protected $state;

    function display( $tpl = null ) {
        $this->state 		= $this->get('State');
        $this->items 		= $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
        $this->canDo        = JHelperContent::getActions('com_myportfolio');
        $this->db           = JFactory::getDbo();

        MyportfolioHelper::addSubmenu('categories');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        parent::display( $tpl );
    }

    protected function addToolbar() {
        $user	= JFactory::getUser();
        $canDo  = $this->canDo;
        $state  = $this->state;

        $jinput = JFactory::getApplication()->input;
        $catid    = $jinput->get('catid', '', 'INT');

        JToolBarHelper::title(JText::_('COM_MYPORTFOLIO_PROJECTS'), 'options');

        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('project.add');
        }

        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('project.edit');
            JToolBarHelper::makeDefault('projects.feature', JText::_('COM_MYPORTFOLIO_FEATURE'));
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::divider();
            JToolBarHelper::publish('projects.publish', JText::_('COM_MYPORTFOLIO_ENABLE'), true);
            JToolBarHelper::unpublish('projects.unpublish', JText::_('COM_MYPORTFOLIO_DISABLE'), true);
            JToolBarHelper::divider();
        }

        if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'projects.delete', 'JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
        } elseif ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('projects.trash');
            JToolBarHelper::divider();
        }

        JHtmlSidebar::setAction('index.php?option=com_myportfolio&view=projects&catid='.$catid);
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
                'a.ordering' => JText::_('COM_MYPORTFOLIO_ORDER'),
                'a.state' => JText::_('COM_MYPORTFOLIO_ENABLED'),
                'a.project' => JText::_('COM_MYPORTFOLIO_NAME'),
                'a.hits' => JText::_('COM_MYPORTFOLIO_HITS'),
                'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}