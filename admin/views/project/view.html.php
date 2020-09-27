<?php
/**
 * MyPortfolio Administrator project view
 * 
 * @package		MyPortfolio.Administrator
 * @subpackage	com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link		https://www.samybaxy.net
 * @license		GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;

/**
 * category view display method
 *
 * @return
 */

class MyportfolioViewProject extends JViewLegacy {
    protected $form;
    protected $item;
    protected $state;
    /**
     * Display the view
     */
    public function display($tpl = null) {
        // Initialise variables.
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state= $this->get('State');
        $this->canDo= JHelperContent::getActions('com_myportfolio');
        $input      = JFactory::getApplication()->input;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        $input->set('hidemainmenu', true);
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()	{
        $user   = JFactory::getUser();
        $userId = $user->get('id');

        $isNew = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        $canDo = $this->canDo;

        if ($isNew) {
            JToolBarHelper::title(JText::_('COM_MYPORTFOLIO_ADD_PROJECT'), 'cogs');
            JToolBarHelper::apply('project.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('project.save', 'COM_MYPORTFOLIO_SAVE_CLOSE');
            JToolBarHelper::save2new('project.save2new');
            JToolbarHelper::cancel('project.cancel');
        } else {
            JToolBarHelper::title(JText::_('COM_MYPORTFOLIO_EDIT_PROJECT'), 'cogs');

            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created == $userId);
            // If not checked out, can save the item.
            if (!$checkedOut && $itemEditable) {
                JToolBarHelper::apply('project.apply');
                JToolBarHelper::save('project.save');
            }

            if ($canDo->get('core.create')) {
                JToolBarHelper::save2new('project.save2new');
            }

            JToolBarHelper::cancel('project.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
