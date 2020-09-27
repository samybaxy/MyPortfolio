<?php
/**
 * MyPortfolio Administrator images controller
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Media component
 *
 * @since  1.0
 */
class MyportfolioViewMediaList extends JViewLegacy
{
    protected $pid;
    protected $catid;
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		if (!$app->isClient('administrator'))
		{
			return $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}

		// Do not allow cache
		$app->allowCache(false);

        $session = JFactory::getSession();
        $this->pid    = $session->get('pid');
        $this->catid  = $session->get('catid');

		$this->images    = $this->get('images');
		$this->documents = $this->get('documents');
		$this->folders   = $this->get('folders');
		$this->videos    = $this->get('videos');
		$this->state     = $this->get('state');

		// Check for invalid folder name
		if (empty($this->state->folder))
		{
			$dirname = JFactory::getApplication()->input->getPath('folder', '');

			if (!empty($dirname))
			{
				$dirname = htmlspecialchars($dirname, ENT_COMPAT, 'UTF-8');
				JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_BROWSE_FOLDER_WARNDIRNAME', $dirname));
			}
		}

		$user = JFactory::getUser();
		$this->canDelete = $user->authorise('core.delete', 'com_myportfolio');

		parent::display($tpl);
	}
}
