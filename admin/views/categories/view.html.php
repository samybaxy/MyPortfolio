<?php
/**
 * MyPortfolio Administrator Categories view
 * 
 * @package		MyPortfolio.Administrator
 * @subpackage	com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link		https://www.samybaxy.net
 * @license		GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;

class MyportfolioViewCategories extends JViewLegacy {
    /**
     * The item data.
     *
     * @var   object
     * @since 1.6
     */
    protected $items;

    /**
     * The pagination object.
     *
     * @var   JPagination
     * @since 1.6
     */
    protected $pagination;

    /**
     * The model state.
     *
     * @var   JObject
     * @since 1.6
     */
    protected $state;

    /**
     * A JForm instance with filter fields.
     *
     * @var    JForm
     * @since  3.6.3
     */
    public $filterForm;

    /**
     * An array with active filters.
     *
     * @var    array
     * @since  3.6.3
     */
    public $activeFilters;

    /**
     * An ACL object to verify user rights.
     *
     * @var    JObject
     * @since  3.6.3
     */
    protected $canDo;

    /**
     * An instance of JDatabaseDriver.
     *
     * @var    JDatabaseDriver
     * @since  3.6.3
     */
    protected $db;

    public function display( $tpl = null ) {

        $this->state        = $this->get('State');
        $this->items 		= $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->filterForm   = $this->get('FilterForm');
        $this->activeFilters= $this->get('ActiveFilters');
        $this->canDo        = JHelperContent::getActions('com_myportfolio');
        $this->db           = JFactory::getDbo();

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display( $tpl );
    }

    protected function addToolbar() {
        $state  = $this->state;
        $canDo  = $this->canDo;
        $user	= JFactory::getUser();

        JToolBarHelper::title(JText::_('COM_MYPORTFOLIO_CATEGORYS'), 'folder-close');

        if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('category.add');
        }

        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('category.edit');
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::divider();
            JToolBarHelper::publish('categories.publish', JText::_('COM_MYPORTFOLIO_ENABLE'), true);
            JToolBarHelper::unpublish('categories.unpublish', JText::_('COM_MYPORTFOLIO_DISABLE'), true);
            JToolBarHelper::divider();
        }

        if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
        } elseif ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('categories.trash');
            JToolBarHelper::divider();
        }

        if ($canDo->get('core.admin') || $canDo->get('core.options'))
        {
            JToolbarHelper::preferences('com_myportfolio');
        }
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
            'a.name' => JText::_('COM_MYPORTFOLIO_NAME'),
            'a.hits' => JText::_('COM_MYPORTFOLIO_HITS'),
            'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}