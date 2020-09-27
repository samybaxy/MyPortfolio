<?php
/**
 * MyPortfolio Administrator projects model
 * 
 * This model handles data when projects are required.
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

// No direct access
defined( '_JEXEC' ) or die;

/**
* MyPortfolio Categories Model
*
* @package    Joomla.Administrator
* @subpackage com_myportfolio
* @since 2.5
*/

class MyportfolioModelProjects extends JModelList {
    //constructor
    public function __construct($config = array()) {
        $config['filter_fields'] = array(
            'id', 'a.id',
            'project', 'a.project',
            'alias', 'a.alias',
            'checked_out', 'a.checked_out',
            'checked_out_time', 'a.checked_out_time',
            'state', 'a.state',
            'access', 'a.access', 'access_level',
            'default', 'a.default',
            'language', 'a.language',
            'description', 'a.description',
            'date', 'a.date',
            'duration', 'a.duration',
            'client', 'a.client',
            'url', 'a.url',
            'short_description', 'a.short_description',
            'catid', 'a.catid',
            'hits', 'a.hits',
            'ordering', 'a.ordering',
            'publish_up', 'a.publish_up',
            'publish_down', 'a.publish_down',
        );

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @since	1.6
     */

    protected function populateState($ordering = 'a.ordering', $direction = 'asc') {
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
        $this->setState('filter.access', $accessId);

        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);

        $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
        $this->setState('filter.language', $language);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '')	{
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.access');
        $id.= ':' . $this->getState('filter.state');
        $id.= ':' . $this->getState('filter.language');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $user	= JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', null, 'int');

        // Select the required fields from the table.
        $query->select($this->getState(
            'list.select',
            'a.id, a.project, a.alias, a.checked_out, a.checked_out_time,' .
            'a.hits, a.default, a.date, a.duration, a.duration, a.client, a.url, ' .
            'a.state, a.access, a.ordering, a.catid, '.
            'a.language, a.publish_up, a.publish_down'
        ));
        $query->from($db->quoteName('#__myportfolio_projects').' AS a');

        // Join over the language
        $query->select('l.title AS language_title')
            ->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor')
            ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level')
            ->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the asset groups.
        $query->select('n.name AS category')
            ->join('LEFT', '#__myportfolio AS n ON n.id = a.catid');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = '.(int) $access);
        }

        //filter by catid
        $query->where($db->quoteName('a.catid').' = '.(int)$catid);

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = '.(int) $access);
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin')) {
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN ('.$groups.')');
        }

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(a.project LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
            }
        }

        // Filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where('a.language = ' . $db->quote($language));
        }

        // Add the list ordering clause
        $listOrdering = $this->getState('list.ordering', 'a.ordering');
        $listDirn = $db->escape($this->getState('list.direction', 'ASC'));

        if ($listOrdering == 'a.access')
        {
            $query->order('a.access ' . $listDirn . ', a.ordering ' . $listDirn);
        }
        else
        {
            $query->order($db->escape($listOrdering) . ' ' . $listDirn);
        }

        return $query;
    }
}