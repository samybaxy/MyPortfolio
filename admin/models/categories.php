<?php
/**
 * MyPortfolio Administrator Categories model
 * 
 * This model handles data when Categories are required.
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
	* MyPorfolio Categories Model
	*
	* @package    Joomla.Administrator
	* @subpackage com_myportfolio
	* @since 2.5
	*/
	
	class MyportfolioModelCategories extends JModelList {

        /**
         * Constructor.
         *
         * @param   array  $config  An optional associative array of configuration settings.
         *
         * @see    JController
         * @since  3.0.3
         */
		public function __construct($config = array()) {
			if (empty($config['filter_fields'])) {
				$config['filter_fields'] = array(
					'id', 'a.id',
					'name', 'a.name',
					'alias', 'a.alias',
					'checked_out', 'a.checked_out',
					'checked_out_time', 'a.checked_out_time',
					'state', 'a.state',
					'access', 'a.access', 'access_level',
					'default', 'a.default',
					'language', 'a.language',
					'hits', 'a.hits',
					'ordering', 'a.ordering',
					'publish_up', 'a.publish_up',
					'publish_down', 'a.publish_down',
					'created', 'a.created',
					'created_by', 'a.created_by',
				);
			}
			
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
			
			// Select the required fields from the table.
			$query->select(
			    $this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time,' .
                    'a.hits,' .
                    'a.state, a.access, a.ordering, a.default,'.
                    'a.language, a.publish_up, a.publish_down'
			)
		);
			$query->from($db->quoteName('#__myportfolio').' AS a');
			
			// Join over the language
			$query->select('l.title AS language_title')
                ->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');
			
			// Join over the users for the checked out user.
			$query->select('uc.name AS editor')
                ->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

			$query->select("(SELECT COUNT(z.id) FROM #__myportfolio_projects AS z WHERE z.catid = a.id) AS count");
			
			// Join over the asset groups.
			$query->select('ag.title AS access_level')
                ->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
			
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
                    $query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
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
		
	public function saveUpload($uploadBind) {			
			//get db
			$db =& JFactory::getDBO();			

			//Get the db and perform a check if an item exist
			$query	= 'SELECT id ';
			$query .= 'FROM #__myportfolio_img ';
			$query .= 'WHERE name = '.$this->_db->Quote($uploadBind['name']);
			
			$db->setQuery($query);
			if( !$db->query() ) {
				$this->setError(JText::_('COM_MYPORTFOLIO_UP_MAP_ROW_FAIL'));
			}
			
			$resultCheck = $db->loadResult();
			
			if (empty($resultCheck) || $resultCheck == NULL) {
				//Perform an update statement
				//$query = '';
				$name = $this->_db->Quote($uploadBind['name']);
				$project = $this->_db->Quote($uploadBind['project']);
				$category = $this->_db->Quote($uploadBind['category']);
				
				$q = 'INSERT INTO #__myportfolio_img (name, category, project, pid) ';
				$q .= 'VALUES ('.$name.', '.$category.', '.$project.', '.(int)$uploadBind['pid'].')';
			
				$db->setQuery($q);
				if( !$db->query() ) {
					$this->setError(JText::_('COM_MYPORTFOLIO_UP_MAP_ROW_FAIL'));
				}
			}  else {
				$qu = 'UPDATE #__myportfolio_img ';
				$qu .= 'SET name = '.$this->_db->Quote($uploadBind['name']).', ';
				$qu .= 'category = '.$this->_db->Quote($uploadBind['category']).', ';
				$qu .= 'project = '.$this->_db->Quote($uploadBind['project']).', ';
				$qu .= 'pid = '.(int)$uploadBind['pid'].' ';
				$qu .= 'WHERE id = '.(int)$resultCheck;

				$db->setQuery($qu);
				if( !$db->query() ) {
					$this->setError(JText::_('COM_MYPORTFOLIO_UP_MAP_ROW_FAIL'));
				}
			}
		
			return true;
		}
	}