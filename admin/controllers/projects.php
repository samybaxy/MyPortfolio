<?php
/**
 * MyPortfolio Administrator Projects controller
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;
use Joomla\Utilities\ArrayHelper;

class MyportfolioControllerProjects extends JControllerAdmin {
    /**
     * The URL option for the component.
     *
     * @var    string
     * @since  1.6
     */
    protected $option;

    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.6
     */
    protected $text_prefix;

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  1.6
     */
    protected $view_list;

    public function __construct($config = array()) {
        parent::__construct($config);

        // Define standard task mappings.

        // Value = 0
        $this->registerTask('feature', 'feature');
        $this->registerTask('cancel', 'cancel');

        // Guess the option as com_NameOfController.
        if (empty($this->option))
        {
            $this->option = 'com_' . strtolower($this->getName());
        }

        // Guess the JText message prefix. Defaults to the option.
        if (empty($this->text_prefix))
        {
            $this->text_prefix = strtoupper($this->option);
        }

        // Guess the list view as the suffix, eg: OptionControllerSuffix.
        if (empty($this->view_list))
        {
            $r = null;

            if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
            {
                throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
            }

            $this->view_list = strtolower($r[2]);
        }
    }

    //New task to set a project as featured
    public function feature() {
        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );

        // Retrieve the ids
        $jinput = JFactory::getApplication()->input;
        $cid    = $jinput->get('cid', null, 'array');
        $catid  = $jinput->get('catid', null, 'int');

        //get cat id from request
        if(empty($catid)) {
            $catid = $jinput->post->get('catid', '', 'int');
        }

        $model =& $this->getModel($name = 'Project', $prefix = 'MyportfolioModel');
        $model->mkDefault($cid, $catid);

        $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false);
        $this->setRedirect( $redirectTo, JText::_('COM_MYPORTFOLIO_PROJECT_FEATURED'));
    }

    //New task to set a project as featured
    public function cancel() {
        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );

        $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=categories', false);
        $this->setRedirect( $redirectTo );
    }

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'Project', $prefix = 'MyportfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * An Override Method to publish a list of items
     *
     * @return  void
     *
     * @since   11.1
     */
    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2);
        $task = $this->getTask();
        $value =  ArrayHelper::getValue($data, $task, 0, 'int');

        //get cat id from request
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', null, 'int');

        if(empty($catid)) {
            $catid = $jinput->post->get('catid', null, 'int');
        }

        if (empty($cid))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_MYPORTFOLIO_NO_ITEM_SELECTED'), 'error');
            $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false);
            $this->setRedirect( $redirectTo );
            return false;
        }
        else
        {

            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            ArrayHelper::toInteger($cid);

            try {

                $model->publish($cid, $value);
                $errors = $model->getErrors();
                $ntext = null;

                if ($value == 1) {
                    if ($errors)
                    {
                        JFactory::getApplication()->enqueueMessage(JText::plural($this->text_prefix . '_N_ITEMS_FAILED_PUBLISHING', count($cid)), 'error');
                    }
                    else
                    {
                        $ntext = 'COM_MYPORTFOLIO_N_ITEMS_PUBLISHED';
                    }
                }

                elseif ($value == 0) {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_UNPUBLISHED';
                }

                elseif ($value == 2) {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_ARCHIVED';
                }

                else {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_TRASHED';
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
            catch (Exception $e)
            {
                $this->setMessage($e->getMessage(), 'error');
            }
        }

        $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false);
        $this->setRedirect( $redirectTo, JText::_($ntext) );
    }

    /**
     * An Override Changes the order of one or more records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function reorder()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $ids = JFactory::getApplication()->input->post->get('cid', array(), 'array');
        $inc = ($this->getTask() == 'orderup') ? -1 : +1;

        //get cat id from request
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', null, 'int');

        if(empty($catid)) {
            $catid = $jinput->post->get('catid', null, 'int');
        }

        $model = $this->getModel();
        $return = $model->reorder($ids, $inc);
        if ($return === false)
        {
            // Reorder failed.
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list.'&catid='.$catid, false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list.'&catid='.$catid, false), $message);
            return true;
        }
    }

    /**
     * An Overide Method to save the submitted ordering values for records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function saveorder() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the input
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        //get cat id from request
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('cid', null, 'int');

        if(empty($catid)) {
            $catid = $jinput->post->get('catid', null, 'int');
        }

        // Sanitize the input
        ArrayHelper::toInteger($pks);
        ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return === false)
        {
            // Reorder failed
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list.'&catid='.$catid, false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list.'&catid='.$catid, false));
            return true;
        }
    }

    /**
     * Removes an item.
     *
     * @return  void
     *
     * @since   11.1
     */
    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');

        //get cat id from request
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', null, 'int');

        if(empty($catid)) {
            $catid = $jinput->post->get('cid', null, 'int');
        }

        if (!is_array($cid) || count($cid) < 1)
        {
            JFactory::getApplication()->enqueueMessage(JText::plural($this->text_prefix . '_NO_ITEM_SELECTED', count($cid)), 'error');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list.'&catid='.$catid, false));
    }
}