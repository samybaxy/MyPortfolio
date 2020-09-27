<?php
/**
 * MyPortfolio Administrator Categories fields model
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

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Myportfolio Form Field class for the Myportfolio component
 */
class JFormFieldMyportfolio extends JFormFieldList {
    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'Myportfolio';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $db = JFactory::getDBO();

        // $query = new JDatabaseQuery; WARNING - There's an error in this line, JDatabaseQuery is an abstract class
        $query = $db->getQuery(true); // THIS IS THE FIX, WARNING IT MUST BE FIXED IN THE ZIP FILES

        $query->select('#__myportfolio.id as id, alias, #__categories.title as category, catid');
        $query->from('#__myportfolio');
        $query->leftJoin('#__categories on catid = #__categories.id');
        $query->where('state = 1');
        $db->setQuery((string)$query);
        $messages = $db->loadObjectList();
        $options = array();
        if ($messages)
        {
                foreach($messages as $message)
                {
                        $options[] = JHtml::_('select.option', $message->id, $message->alias .
                                              ($message->catid ? ' (' . $message->category . ')' : ''));
                }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}