<?php
/**
 * MyPortfolio Administrator Category model
 * 
 * This model handles data when category(s) is required.
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

	// No direct access
	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	class MyportfolioModelCategory extends JModelAdmin {
		/**
		 * @var		string	The prefix to use with controller messages.
		 * @since	1.6
		 */
		
		protected $text_prefix = 'COM_MYPORTFOLIO';
		
		/**
		 * Method to test whether a record can be deleted.
		 *
		 * @param	object	A record object.
		 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
		 * @since	1.6
		 */
		protected function canDelete($record)
		{
			if (!empty($record->id)) {
				if ($record->state != -2) {
					return;
				}
				
				$user = JFactory::getUser();
		
				if ($record->id) {
					return $user->authorise('core.delete', 'com_myportfolio.category.'.(int) $record->id);
				}
				else {
					return parent::canDelete($record);
				}
			}
		}
		
		/**
		* Method to test whether a record can have its state changed.
		*
		* @param object A record object.
		* @return Boolean True if allowed to change the state of the record.
		* Defaults to the permission set in the component.
		*/
		protected function canEditState($record) {
			$user = JFactory::getUser();
			if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_myportfolio.category.'.(int) $record->id);
			}
			else {
				return parent::canEditState($record);
			}
		}		
		
		/**
		 * Returns a reference to the a Table object, always creating it.
		 *
		 * @param type The table type to instantiate
		 * @param string A prefix for the table class name. Optional.
		 * @param array Configuration array for model. Optional.
		 * @return JTable A database object
		 */
		
		public function getTable($type = 'Category', $prefix = 'MyportfolioTable', $config = array()) {
			return JTable::getInstance($type, $prefix, $config);
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
		public function getForm($data = array(), $loadData = true) {
			// Initialise variables.
			$app = JFactory::getApplication();
			// Get the form.
			$form = $this->loadForm('com_myportfolio.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
				if (empty($form)) {
					return false;
				}
			// Determine correct permissions to check.
			if ($this->getState('category.id')) {
				// Existing record. Can only edit in selected categories.
				$form->setFieldAttribute('id', 'action', 'core.edit');
			} else {
				// New record. Can only create in selected categories.
				$form->setFieldAttribute('id', 'action', 'core.create');
			}
			// Modify the form based on access controls.
			if (!$this->canEditState((object) $data)) {
				// Disable fields for display.
				$form->setFieldAttribute('ordering', 'disabled', 'true');
				$form->setFieldAttribute('state', 'disabled', 'true');
				$form->setFieldAttribute('publish_up', 'disabled', 'true');
				$form->setFieldAttribute('publish_down', 'disabled', 'true');
				// Disable fields while saving.
				// The controller has already verified this is a record you can
				//edit.
				$form->setFieldAttribute('ordering', 'filter', 'unset');
				$form->setFieldAttribute('state', 'filter', 'unset');
				$form->setFieldAttribute('publish_up', 'filter', 'unset');
				$form->setFieldAttribute('publish_down', 'filter', 'unset');
			}
			
			return $form;
		}
		
		/**
		 * Method to get the data that should be injected in the form.
		 *
		 * @return mixed The data for the form.
		 */
		protected function loadFormData() {
			// Check the session for previously entered form data.
			$data = JFactory::getApplication()->getUserState('com_myportfolio.edit.category.data', array());
			if (empty($data)) {
				$data = $this->getItem();
				// Prime some default values.
				if ($this->getState('category.id') == 0) {
					$app = JFactory::getApplication();
					$data->set('id', JFactory::getApplication()->input->getInt('id', $app->getUserState('com_myportfolio.category.filter.category_id')));
				}
			}
			return $data;
		}
		
		
		/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry;
		
		}

		if ($item = parent::getItem($pk))
		{
			// Convert the images field to an array.
			$registry = new JRegistry;
		}

		return $item;
	}
		
		/**
		 * Prepare and sanitise the table prior to saving.
		 *
		 */
		protected function prepareTable($table) {
			$date = JFactory::getDate();
			$user = JFactory::getUser();
	
			$table->name		= htmlspecialchars_decode($table->name, ENT_QUOTES);
			$table->alias		= JApplicationHelper::stringURLSafe($table->alias);
	
			if (empty($table->alias)) {
				$table->alias = JApplicationHelper::stringURLSafe($table->name);
			}
	
			if (empty($table->id)) {
				// Set the values
	
				// Set ordering to the last item if not set
				if (empty($table->ordering)) {
					$db = JFactory::getDbo();
					$db->setQuery('SELECT MAX(ordering) FROM #__myportfolio');
					$max = $db->loadResult();
	
					$table->ordering = $max+1;
				}
			}
			else {
				// Set the values
			}
		}
		
		/**
		 * A protected method to get a set of ordering conditions.
		 *
		 * @param	$table	A record object.
		 * @return	array	An array of conditions to add to add to ordering queries.
		 * @since	1.6
		 */
		protected function getReorderConditions($table)
		{
			$condition = array();
			$condition[] = 'id = '.(int) $table->id;
			return $condition;
		}

		/**
		 * Method to make an individual portfolio category default on save
		 *
		 * @access	public
		 */
		public function mkFeat() {
			$db =& JFactory::getDbo();
			$query = $db->getQuery(true);
				
			//check first for a default category
			$query->select('id');
			$query->from($db->quoteName('#__myportfolio'));
			$query->where($db->quoteName('state').' = 1');
			$query->where($db->quoteName('default').' = 1');
		
			$db->setQuery($query);
			$df = $db->loadResult();
			
			//check first for an id
			$query->clear();
			$query->select('id');
			$query->from($db->quoteName('#__myportfolio'));
			$query->where($db->quoteName('state').' = 1');
			
			$db->setQuery($query);
			$id = $db->loadResult();
				
			if(!$df) {
			    try {
			        $db->transactionStart();
                    $query->clear();
                    $query->update($db->quoteName('#__myportfolio'));
                    $query->set($db->quoteName('default') . ' = 0');

                    $db->setQuery($query);
                    $db->execute();
                    $db->transactionCommit();
                }
                catch (Exception $e) {
                    // catch any database errors.
                    $db->transactionRollback();
                    JErrorPage::render($e);
                }

                try {
                    $query->clear();
                    $query->update($db->quoteName('#__myportfolio'));
                    $query->set($db->quoteName('default') . ' = 1');
                    $query->where($db->quoteName('id') . ' = ' . (int)$id);

                    $db->setQuery($query);
                    $db->execute();
                    $db->transactionCommit();
                }
                catch (Exception $e) {
                    // catch any database errors.
                    $db->transactionRollback();
                    JErrorPage::render($e);
                }
			}
		}
	}