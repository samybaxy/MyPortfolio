<?php
/**
 * MyPortfolio Administrator toolbar layout
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;

$title = JText::_('JTOOLBAR_DELETE');
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
?>
<script type="text/javascript">
(function($){
	// if any media is selected then only allow to submit otherwise show message
	deleteMedia = function(){
		if ( $('#folderframe').contents().find('input:checked[name="rm[]"]').length == 0){
			alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
			return false;
		}

	    MediaManager.submit('folder.delete');
	};

})(jQuery);
</script>

<button onclick="deleteMedia()" class="btn btn-small">
	<span class="icon-remove" title="<?php echo $title; ?>"></span> <?php echo $title; ?>
</button>
