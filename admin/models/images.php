<?php
/**
 * MyPortfolio Administrator Images model
 * 
 * This model handles data when Images are required.
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

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

/**
* MyPortfolio Categories Model
*
* @package    Joomla.Administrator
* @subpackage com_myportfolio
* @since 2.5
*/

//import Joomla folder system
jimport('joomla.filesystem.folder');

class MyportfolioModelImages extends JModelList {

     //constructor
    public function __construct($config = array()) {
        $config['filter_fields'] = array(
            'id', 'a.id',
            'name', 'a.name',
            'category', 'a.category',
            'project', 'a.project',
            'access', 'a.access',
            'state', 'a.state',
            'default', 'a.default',
            'ordering', 'a.ordering',
        );

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param string $ordering
     * @param string $direction
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */

    protected function populateState($ordering = 'a.ordering', $direction = 'asc') {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

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
        //$id.= ':' . $this->getState('filter.category_id');
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

        // Select the required fields from the table.
        $query->select($this->getState(
            'list.select',
            'a.id, a.name, a.category, a.project,' .
            'a.default, a.access, a.pid,' .
            'a.state, a.ordering'
        ));
        $query->from($db->quoteName('#__myportfolio_img').' AS a');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        $jinput = JFactory::getApplication()->input;
        $pid = $jinput->get->get('pid', null, 'int');

        if(empty($pid)) {
            $pid = $jinput->post->get('pid', null, 'int');
        }

        $query->where('a.pid = '.(int)$pid);

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'a.name '.$orderDirn.', a.ordering';
        }
        $query->order($db->escape($orderCol.' '.$orderDirn));
        return $query;
    }

    //Method to retrieve category and project names
    public function getCatProjectName($cid, $pid) {
        $data = array();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('alias'))
            ->from($db->quoteName('#__myportfolio'))
            ->where($db->quoteName('id').' = '.(int)$cid);

        $db->setQuery($query);
        $data[] =$db->loadResult();

        if(!empty($data[0])) {
            $query->clear()
                ->select($db->quoteName('alias'))
                ->from($db->quoteName('#__myportfolio_projects'))
                ->where($db->quoteName('id').' = '.(int)$pid);

            $db->setQuery($query);
            $data[] = $db->loadResult();
        }

        return $data;
    }

    /**
     * Method to get the record form.
     *
     * @param array $data An optional array of data for the form to
     * interogate.
     * @param boolean $loadData True if the form is to load its own
     * data (default case), false if not.
     * @return JForm A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = false) {
        // Initialise variables.
        $app = JFactory::getApplication();
        // Get the form.
        $form = $this->loadForm('com_myportfolio.images', 'images', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    public function getImages() {
        $session 	= JFactory::getSession();
        $subfolders	= $session->get('pathToFiles');
        $path = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$subfolders;

        $files = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));

        return $files;
    }
}