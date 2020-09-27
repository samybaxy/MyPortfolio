<?php
/**
 * MyPortfolio Administrator Project model
 * 
 * This model handles data when project(s) is required.
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

class MyportfolioModelProject extends JModelAdmin {
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
                return $user->authorise('core.delete', 'com_myportfolio.project.'.(int) $record->id);
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
        return $user->authorise('core.edit.state', 'com_myportfolio.project.'.(int) $record->id);
        }
        else {
            return parent::canEditState($record);
        }
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string The table type to instantiate
     * @param string A prefix for the table class name. Optional.
     * @param array Configuration array for model. Optional.
     * @return JTable A database object
     */

    public function getTable($type = 'Project', $prefix = 'MyportfolioTable', $config = array()) {
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
        $form = $this->loadForm('com_myportfolio.project', 'project', array('control' => 'jform', 'load_data' => $loadData));
            if (empty($form)) {
                return false;
            }
        // Determine correct permissions to check.
        if ($this->getState('project.id')) {
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
        $data = JFactory::getApplication()->getUserState('com_myportfolio.edit.project.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     */
    protected function prepareTable($table) {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $table->project		= htmlspecialchars_decode($table->project, ENT_QUOTES);
        $table->alias		= JApplicationHelper::stringURLSafe($table->alias);
        $parsed = parse_url($table->url);
        if (empty($parsed['scheme'])) {
            $table->url = 'http://' . ltrim($table->url, '/');
        }

        if (empty($table->alias)) {
            $table->alias = JApplicationHelper::stringURLSafe($table->project);
        }

        if (empty($table->id)) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__myportfolio_projects');
                $max = $db->loadResult();

                $table->ordering = $max+1;
            }
        }
        else {
            // Set the values
        }
    }

    /**
     * Method to make an individual portfolio category default
     *
     * @param $cid array
     * @access	public
     */
    public function mkDefault($cid, $catid) {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);

        try{
            $db->transactionStart();
            $query->update($db->quoteName('#__myportfolio_projects'))
                ->set($db->quoteName('default').' = 0')
                ->where($db->quoteName('catid').' = '.(int)$catid);

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
            $db->transactionStart();
            $query->clear();
            $query->update($db->quoteName('#__myportfolio_projects'))
                ->set($db->quoteName('default') . ' = 1')
                ->where($db->quoteName('id') . ' = ' . (int)$cid[0])
                ->where($db->quoteName('catid') . ' = ' . (int)$catid);

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

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param	object	A record object.
     * @return	array	An array of conditions to add to add to ordering queries.
     * @since	1.6
     */
    protected function getReorderConditions($table) {
        $condition = array();
        $condition[] = 'id = '.(int) $table->id;
        return $condition;
    }

    /**
     * Override method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   12.2
     */
    public function save($data)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $table = $this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());
                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());
                return false;
            }

            // Trigger the onContentBeforeSave event.
            $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());
                return false;
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the onContentAfterSave event.
            $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName))
        {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        //method to make category featured.
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', '', 'int');

        $this->mkFeat($catid);

        return true;
    }

    /**
     * Method to make an individual portfolio project default on save
     * @param int $cid
     * @access	public
     */
    public function mkFeat($cid) {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);

        //check first for a default category
        try {
            $db->transactionStart();
            $query->select('id');
            $query->from($db->quoteName('#__myportfolio_projects'));
            $query->where($db->quoteName('state') . ' = 1');
            $query->where($db->quoteName('default') . ' = 1');
            $query->where($db->quoteName('catid') . ' = ' . (int)$cid);

            $db->setQuery($query);
            $df = $db->loadResult();
            $db->transactionCommit();
        }
        catch (Exception $e) {
            // catch any database errors.
            $db->transactionRollback();
            JErrorPage::render($e);
        }

        //check first for an id
        try {
            $query->clear();
            $query->select('id');
            $query->from($db->quoteName('#__myportfolio_projects'));
            $query->where($db->quoteName('state') . ' = 1');
            $query->where($db->quoteName('catid') . ' = ' . (int)$cid);

            $db->setQuery($query);
            $id = $db->loadResult();
            $db->transactionCommit();
        }
        catch (Exception $e) {
            // catch any database errors.
            $db->transactionRollback();
            JErrorPage::render($e);
        }

        if(!$df) {
            try {
                $db->transactionStart();
                $query->clear();
                $query->update($db->quoteName('#__myportfolio_projects'));
                $query->set($db->quoteName('default') . ' = 0');
                $query->where($db->quoteName('catid') . ' = ' . (int)$cid);

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
                $db->transactionStart();
                $query->clear();
                $query->update($db->quoteName('#__myportfolio_projects'));
                $query->set($db->quoteName('default') . ' = 1');
                $query->where($db->quoteName('id') . ' = ' . (int)$id);
                $query->where($db->quoteName('catid') . ' = ' . (int)$cid);

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