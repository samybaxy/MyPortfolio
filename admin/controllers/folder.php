<?php
/**
 * MyPortfolio Administrator folder controller
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Folder Media Controller
 *
 * @since  1.5
 */
class MyportfolioControllerFolder extends JControllerLegacy
{
	/**
	 * Deletes paths from the current path
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function delete()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		// Get some data from the request
        $pid    = $this->input->get('pid', '', 'INT');
        $catid  = $this->input->get('catid', '', 'INT');
		$paths  = $this->input->get('rm', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

        $redirect = "index.php?option=com_myportfolio&view=images&pid={$pid}&catid={$catid}&folder={$folder}";
		$this->setRedirect($redirect);

		// Just return if there's nothing to do
		if (empty($paths))
		{
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');

			return true;
		}

		if (!$user->authorise('core.delete', 'com_myportfolio'))
		{
			// User is not authorised to delete
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Need this to enqueue messages.
		$app = JFactory::getApplication();

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;

		$safePaths = array_intersect($paths, array_map(array('JFile', 'makeSafe'), $paths));
		$unsafePaths = array_diff($paths, $safePaths);

		foreach ($unsafePaths as $path)
		{
			$path = JPath::clean(implode(DIRECTORY_SEPARATOR, array($folder, $path)));
			$path = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
			$app->enqueueMessage(JText::sprintf('COM_MYPORTFOLIO_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', $path), 'error');
		}

		foreach ($safePaths as $path)
		{
			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(JPATH_SITE."/images", $folder, $path)));
			$object_file = new JObject(array('filepath' => $fullPath));

			if (is_file($object_file->filepath))
			{
				$ret &= JFile::delete($object_file->filepath);
            }

			elseif (is_dir($object_file->filepath))
			{
				$contents = JFolder::files($object_file->filepath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

				if (!empty($contents))
				{
					// This makes no sense...
					$folderPath = substr($object_file->filepath, strlen(JPATH_SITE."/images"));
					JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $folderPath));

					continue;
				}

				$ret &= !JFolder::delete($object_file->filepath);
			}
		}

        $app->enqueueMessage(JText::sprintf('COM_MYPORTFOLIO_IMAGES_DELETED_SUCCESSFULLY', $path), 'notice');
		return $ret;
	}

	/**
	 * Create a folder
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function create()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user  = JFactory::getUser();

		$folder      = $this->input->get('foldername', '');
		$folderCheck = (string) $this->input->get('foldername', null, 'raw');
		$parent      = $this->input->get('folderbase', '', 'path');

		$this->setRedirect('index.php?option=com_media&folder=' . $parent . '&tmpl=' . $this->input->get('tmpl', 'index'));

		if (strlen($folder) > 0)
		{
			if (!$user->authorise('core.create', 'com_media'))
			{
				// User is not authorised to create
				JError::raiseWarning(403, JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED'));

				return false;
			}

			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			$this->input->set('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'), 'warning');

				return false;
			}

			$path = JPath::clean(COM_MEDIA_BASE . '/' . $parent . '/' . $folder);

			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new JObject(array('filepath' => $path));
				JPluginHelper::importPlugin('content');
				$dispatcher = JEventDispatcher::getInstance();
				$result     = $dispatcher->trigger('onContentBeforeSave', array('com_media.folder', &$object_file, true));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));

					return false;
				}

				if (JFolder::create($object_file->filepath))
				{
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					JFile::write($object_file->filepath . '/index.html', $data);

					// Trigger the onContentAfterSave event.
					$dispatcher->trigger('onContentAfterSave', array('com_media.folder', &$object_file, true));
					$this->setMessage(JText::sprintf('COM_MEDIA_CREATE_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
				}
			}

			$this->input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);
		}
		else
		{
			// File name is of zero length (null).
			JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));

			return false;
		}

		return true;
	}
}
