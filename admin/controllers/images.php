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

// no direct access
defined( '_JEXEC' ) or die;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class MyportfolioControllerImages extends JControllerAdmin {

    /**
     * The folder we are uploading into
     *
     * @var   string
     */
    protected $folder = '';
    protected $text_prefix = 'COM_MYPORTFOLIO';

    public function __construct($config = array()) {
        parent::__construct($config);

        // Define standard task mappings.
        $this->registerTask('cancel', 'cancel');
        $this->registerTask('upload', 'upload');
    }

    /**
     * Upload one or more files
     *
     * @return  boolean
     *
     * @since   1.5
     */
    public function upload()
    {
        // Check for request forgeries
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
        $params = JComponentHelper::getParams('com_media');

        // Get some data from the request
        $files        = $this->input->files->get('Filedata', array(), 'array');
        $return       = JFactory::getSession()->get('com_myportfolio.return_url');
        $this->folder = $this->input->get('folder', '', 'path');

        // Don't redirect to an external URL.
        if (!JUri::isInternal($return))
        {
            $return = '';
        }

        // Set the redirect
        if ($return)
        {
            $this->setRedirect($return. '&folder=' . $this->folder);
        }
        else
        {
            $this->setRedirect('index.php?option=com_myportfolio&folder=' . $this->folder);
        }

        // Authorize the user
        if (!$this->authoriseUser('create'))
        {
            return false;
        }

        // If there are no files to upload - then bail
        if (empty($files))
        {
            return false;
        }

        // Total length of post back data in bytes.
        $contentLength = (int) $_SERVER['CONTENT_LENGTH'];

        // Instantiate the media helper
        $mediaHelper = new JHelperMedia;

        // Maximum allowed size of post back data in MB.
        $postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

        // Maximum allowed size of script execution in MB.
        $memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

        // Check for the total size of post back data.
        if (($postMaxSize > 0 && $contentLength > $postMaxSize)
            || ($memoryLimit != -1 && $contentLength > $memoryLimit))
        {
            JError::raiseWarning(100, JText::_('COM_MYPORTFOLIO_ERROR_WARNFILETOOLARGE'));

            return false;
        }

        $uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
        $uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

        // Perform basic checks on file info before attempting anything
        foreach ($files as &$file)
        {
            $file['name']     = JFile::makeSafe($file['name']);
            $file['name']     = str_replace(' ', '-', $file['name']);
            $file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(JPATH_SITE."/images", $this->folder, $file['name'])));

            if (($file['error'] == 1)
                || ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
                || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
            {
                // File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
                JError::raiseWarning(100, JText::_('COM_MYPORTFOLIO_ERROR_WARNFILETOOLARGE'));

                return false;
            }

            if (JFile::exists($file['filepath']))
            {
                // A file with this name already exists
                JError::raiseWarning(100, JText::_('COM_MYPORTFOLIO_ERROR_FILE_EXISTS'));

                return false;
            }

            if (!isset($file['name']))
            {
                // No filename (after the name was cleaned by JFile::makeSafe)
                $this->setRedirect('index.php', JText::_('COM_MYPORTFOLIO_UPLOAD_ERROR_NO_FILE'), 'error');

                return false;
            }
        }

        // Set FTP credentials, if given
        JClientHelper::setCredentialsFromRequest('ftp');

        foreach ($files as &$file)
        {
            // The request is valid
            $err = null;

            $object_file = new JObject($file);
            if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
            {
                // Error in upload
                JError::raiseWarning(100, JText::_('COM_MYPORTFOLIO_FILE_MOVE_ERROR'));

                return false;
            }

            $this->setMessage(JText::sprintf('COM_MYPORTFOLIO_FILES_UPLOADED', substr($object_file->filepath, strlen($this->folder))));
        }

        $this->setRedirect($return. '&folder=' . $this->folder);
        return true;
    }

    //New task to set a project as featured
    public function cancel() {
        //get cat id from request
        $jinput = JFactory::getApplication()->input;
        $catid = $jinput->get('catid', null, 'int');

        if(empty($catid)) {
            $catid = $jinput->post->get('catid', null, 'int');
        }

        $redirectTo = JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false);
        $this->setRedirect( $redirectTo );
    }

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'Image', $prefix = 'MyportfolioModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Removes an item.
     *
     * @return  void
     *
     * @since   11.1
     */
    public function delete() {
        // Check for request forgeries
        JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));

        $user   = JFactory::getUser();

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
                    JError::raiseWarning(100, JText::sprintf('COM_MYPORTFOLIO_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $folderPath));

                    continue;
                }

                $ret &= !JFolder::delete($object_file->filepath);
            }
        }

        $app->enqueueMessage(JText::sprintf('COM_MYPORTFOLIO_IMAGES_DELETED_SUCCESSFULLY', $path), 'notice');
        return $ret;
    }

    /**
     * Check that the user is authorized to perform this action
     *
     * @param   string  $action  - the action to be peformed (create or delete)
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function authoriseUser($action)
    {
        if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_myportfolio'))
        {
            // User is not authorised
            JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));

            return false;
        }

        return true;
    }
}