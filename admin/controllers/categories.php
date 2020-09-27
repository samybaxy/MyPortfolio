<?php
/**
 * MyPortfolio Administrator Categories controller
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

class MyportfolioControllerCategories extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);

        // Define standard task mappings.

        // Value = 0
        $this->registerTask('feature', 'feature');
    }

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'Category', $prefix = 'MyportfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JLog::add(JText::_('COM_MYPORTFOLIO_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            if (!$model->publish($cid, $value))
            {
                JLog::add($model->getError(), JLog::WARNING, 'jerror');
            }
            else
            {
                if ($value == 1)
                {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_UNPUBLISHED';
                }
                elseif ($value == 2)
                {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_ARCHIVED';
                }
                else
                {
                    $ntext = 'COM_MYPORTFOLIO_N_ITEMS_TRASHED';
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }

        $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=categories', false);
        $this->setRedirect( $redirectTo, JText::_($ntext) );
    }
}