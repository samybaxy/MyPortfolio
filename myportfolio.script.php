<?php
/**
 * MyPortfolio Install
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy 
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 * 
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined( '_JEXEC' ) or die;

/**
 * Script file of myPortfolio component
 */

class com_myportfolioInstallerScript {
	function install($parent) {
		echo JText::_('<p style="color: #3c763d;">Thank you for installing MyPortfolio. Hours of dedicated time away from my paying projects went into this</p>
<p style="color: #3c763d;">If you appreciate this work, please make a donation to support this project which has been going on for over 10 years, no support & over 8000+ downloads
				 <a href="https://www.samybaxy.net/forum/announcements-updates-bug-fixes/232-myportfolio-4-will-be-launched-today" target="_blank">Support Samybaxy</a>.
				 Thank you again</p>');
		return true;
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) {
		// $parent is the class calling this method
	echo JText::_('COM_MYPORTFOLIO_UNINSTALL_TXT');
	}
	
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::sprintf('COM_MYPORTFOLIO_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}
}