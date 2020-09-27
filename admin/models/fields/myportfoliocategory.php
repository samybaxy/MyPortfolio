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
 * @license	      GNU/GPLv3 or Later
 */

// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Myportfolio Form Field class for the Myportfolio component
 */
class JFormFieldMyportfolioCategory extends JFormFieldList {
        /**
         * The field type.
         *
         * @var         string
         */
        protected $type = 'myportfoliocategory';
 
        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        protected function getOptions() {
                $db = JFactory::getDBO();
 
                /// $query = new JDatabaseQuery; WARNING - There's an error in this line, JDatabaseQuery is an abstract class
                $query = $db->getQuery(true); // THIS IS THE FIX, WARNING IT MUST BE FIXED IN THE ZIP FILES
 
                $query->select('a.alias, a.id');
                $query->from('#__myportfolio AS a');
                $query->where('a.state = 1');
                $query->order('a.ordering ASC');
                $db->setQuery((string)$query);
                $messages = $db->loadObjectList();
                $options = array();
                if ($messages)
                {
                        foreach($messages as $message) 
                        {
                                $options[] = JHtml::_('select.option', $message->id, $message->alias);
                        }
                }
                
                $options = array_merge(parent::getOptions(), $options);
                return $options;
        }
}