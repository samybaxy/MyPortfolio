<?php
/**
 * MyPortfolio Site Model
 *
 * @package	      MyPortfolio.Site
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

//import Joomla folder system
jimport('joomla.filesystem.folder');


class MyportfolioModelMyportfolio extends JModelLegacy {

    //This method is used for returning one default project
    public function getPortfolio() {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', '', 'INT');

        //get db and assoc table names
        $db 	= JFactory::getDBO();
        $query	= $db->getQuery(true);

        $cid 	= $db->qn('catid');
        $default= $db->qn('default');

        //rid = category_id from the #__myportfolio database
        $rid 	= $db->qn('id');
        //@todo may need to remove this line - $pid 	= $db->qn('pid');
        $pub 	= $db->qn('state');
        $table1 = $db->qn('#__myportfolio');
        $table2 = $db->qn('#__myportfolio_projects');

        $query->select('*')
            ->from($table1)
            ->where("{$pub} = 1")
            ->where("{$rid} = ".(int)$id);

        $db->setQuery( $query );
        $category = $db->loadObject();

        if ( $category === null ) {
            throw new Exception(JText::_('Category not found'), 500);
        }

        //hit counter for category
        $this->_category($category->id);

        // Return the portfolio object
        $query->clear();
        $query->select('*')
            ->from($table2)
            ->where("{$cid} = ".(int)$category->id)
            ->where("{$default} = 1");

        $db->setQuery( $query );

        //Retrieved default project
        $project = $db->loadObject();

        if ( $project === null ) {
            echo "Default Project not found";
            return false;
        }

        $this->_project($project->id);

        $portfolio[0] = $category;
        $portfolio[1] = $project;

        //return the array objects
        return $portfolio;
    }

    //Used by each template's display view method
    //To retrieve projects underneath requested category
    //this is used by the projects nav bar on select templates
    function getCatProjects() {
        $input  = JFactory::getApplication()->input;
        $id     = $input->get('id', '', 'INT');
        $data   = array();

        $db		= JFactory::getDBO();
        $query	= $db->getQuery(true);

        //$default= $db->qn('default');
        //$pid    = $db->qn('pid');

        //category_id for #__myportfolio table
        $rid    = $db->qn('id');
        $cid    = $db->qn('catid');
        $pub    = $db->qn('state');
        $table1 = $db->qn('#__myportfolio');
        $table2 = $db->qn('#__myportfolio_projects');

        $query->select('*')
            ->from($table1)
            ->where("{$pub} = 1")
            ->where("{$rid} = ".(int)$id);

        $db->setQuery( $query );

        $category = $db->loadObject();
        $this->_category($category->id);

        $data[0] = $category->alias;

        if(!empty($category)) {
            $query->clear();
            $query->select('*')
                ->from($table2)
                ->where("{$cid} = ".(int)$category->id)
                ->where("{$pub} = 1");

            $db->setQuery( $query );
            $data[1] = $db->loadObjectList();

            foreach($data[1] as $k => $v) {
                $this->_project($v->id);
            }
        }

        if ( $data[1] === null ) {
            throw new Exception(JText::_('projects were not found'), 500);
        }

        return $data;
    }

    //This retrieves the requested project
    function getProject() {
        //getVar
        $input = JFactory::getApplication()->input;
        $id = $input->get('pid', '', 'INT');

        $db 	= JFactory::getDBO();
        $query	= $db->getQuery(true);

        //$cid  = $db->quoteName('catid');
        //$pid  = $db->quoteName('pid');
        $idx    = $db->qn('id');
        $pub    = $db->qn('state');
        $table1 = $db->qn('#__myportfolio');
        $table2 = $db->qn('#__myportfolio_projects');

        $query->select("*")
            ->from($table2)
            ->where("{$idx} = " . (int)$id);

        $db->setQuery( $query );
        $project = $db->loadObject();

        if ( $project === null ) {
            throw new Exception(JText::_('The Portfolio project was not found.'), 500);
        }
        else {
            $query->clear();
            $query->select('*')
                ->from($table1)
                ->where("{$pub} = 1")
                ->where("{$idx} = ".(int)$project->catid);

            $db->setQuery( $query );
            $category = $db->loadObject();

            //hit counter for a single project
            $this->_project($id);

            $data = array();
            $data[] = $project;

            $path = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$category->alias.DS.$project->alias;
            $data[] = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
            $data[] = $category->alias;

            return $data;
        }
    }

    //Used by select template's display view method
    //To retrieve only featured default category projects and
    //all state images under each project - reel
    function getProjectsPaginate() {
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', '', 'INT');

        //get Component wide parameters
        $params = JComponentHelper::getParams('com_myportfolio');
        $max = $params->get('projectNo');
        if(!$max) {
            $max = 5;
        }

        //Get param config
        $page = $input->get('page', 1, 'INT');
        $cur = (($page * $max) - $max);

        $db 	= JFactory::getDBO();
        $query	= $db->getQuery(true);

        $cid    = $db->qn('catid');
        $rid    = $db->qn('id');
        $ord    = $db->qn('ordering');
        $pub    = $db->qn('state');
        $table1 = $db->qn('#__myportfolio');
        $table2 = $db->qn('#__myportfolio_projects');

        $query->select('*')
            ->from($table1)
            ->where("{$pub} = 1")
            ->where("{$rid} = ".(int)$id);

        $db->setQuery( $query );
        $category = $db->loadObject();

        $query->clear();
        $query->select('*')
            ->from($table2)
            ->where("{$cid} = ".(int)$category->id)
            ->where("{$pub} = 1");

        $db->setQuery( $query );
        $rows = $db->loadObjectList();
        $countTotal = count($rows);

        $totalPages = ceil($countTotal / $max);

        //hit counter for category
        $this->_category($id);

        //Use joomla session object to save the totalPages variable
        $session = JFactory::getSession();
        $session->set('totalPgs', $totalPages);

        $query->clear();
        $query->select("*")
            ->from($table2)
            ->where("{$cid} = ".(int)$id)
            ->where("{$pub} = 1")
            ->order("{$ord} ASC");

        $db->setQuery($query, $cur, $max);
        $projects = $db->loadObjectList();
        if ( $projects === null ) {
            throw new Exception(JText::_('The Portfolio project id(s) was not found.'), 500);
        }

        else {
            // Return the portfolio project
            $data = array();
            foreach ($projects as $i => $project) {
                $data[$i][] = $project;

                //set images
                $path = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$category->alias.DS.$project->alias;
                $data[$i][] = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                $data[$i][] = $category->alias;
            }

            return $data;
        }
    }

    //Once a category is clicked, the featured project under that
    //specific category will be retrieved
    function getFeatPort($id) {
        $db     = JFactory::getDBO();
        $query  = $db->getQuery(true);

        $cid    = $db->qn('catid');
        $pid    = $db->qn('pid');
        $pub    = $db->qn('state');
        $default= $db->qn('default');
        $table2 = $db->qn('#__myportfolio_projects');

        //hit counter for category
        $this->_category($id);

        // Return the portfolio object
        $query->select('*')
            ->from($table2)
            ->where("{$cid} = ".(int)$id)
            ->where("{$default} = 1");

        $db->setQuery( $query );
        $data = $db->loadObjectList();

        if ( $data === null ) {
            throw new Exception(JText::_('The Portfolio project was not found.'), 500);
        }

        $data[1] = '';
        //call private method and add its returned data
        //to the request variable
        $data[2] = $this->_getPortList($id);
        return $data;
    }

    //Private function for incrementing
    //the hits counter for the category table
    private function _category($id) {
        $db 	= JFactory::getDBO();
        $query 	= $db->getQuery(true);
        $cid 	= $db->qn('id');
        $hits 	= $db->qn('hits');
        $table1 = $db->qn('#__myportfolio');

        try {
            $db->transactionStart();
            $query->update($table1)
                ->set($db->qn('hits')." = 1 +" .$hits)
                ->where("{$cid} = " . (int)$id);

            $db->setQuery($query);
            $db->execute();
            $db->transactionCommit();
        }
        catch (Exception $e) {
            $db->transactionRollback();
            JErrorPage::render($e);
        }
    }

    //Private function for incrementing
    //the hits counter for the project/ portfolio item table
    private function _project($id) {
        $db 	= JFactory::getDBO();
        $query 	= $db->getQuery(true);
        $pid 	= $db->qn('id');
        $hits 	= $db->qn('hits');
        $table2 = $db->qn('#__myportfolio_projects');

        try {
            $db->transactionStart();
            $query->update($table2)
                ->set($db->qn('hits')." = 1 +" .$hits)
                ->where("{$pid} = " . (int)$id);

            $db->setQuery($query);
            $db->transactionCommit();
        }
        catch(Exception $e) {
            $db->transactionRollback();
            JErrorPage::render($e);
        }

    }

    //private function for portfolio projects under a specific category
    function _getPortList($id) {
        $db 	= JFactory::getDBO();
        $query 	= $db->getQuery(true);
        $cid    = $db->qn('catid');
        $pid    = $db->qn('pid');
        $pub    = $db->qn('state');
        $table2 = $db->qn('#__myportfolio_projects');
        $default= $db->qn('state');

        $query->select('*')
            ->from($table2)
            ->where("{$cid} = ".(int)$id)
            ->where("{$pub} = 1");

        $db->setQuery( $query );
        $portList = $db->loadObjectList();

        if ( $portList === null ) {
            throw new Exception(JText::_('The Portfolio project id(s) was not found.'), 500);
        }

        return $portList;
    }
}