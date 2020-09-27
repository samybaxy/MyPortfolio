<?php
/**
 * MyPortfolio Administrator medialist view
 * @package	      MyPortfolio.Administrator
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

defined('_JEXEC') or die;
$params = JComponentHelper::getParams('com_media');
$path   = 'file_path';

JHtml::_('jquery.framework');
JHtml::_('behavior.core');

$doc = JFactory::getDocument();

// Need to override this core function because we use a different form id
$doc->addScriptDeclaration(
	"
		jQuery(document).ready(function($){
			window.parent.document.updateUploader();
			$('.img-preview, .preview').each(function(index, value) {
				$(this).on('click', function(e) {
					window.parent.jQuery('#imagePreviewSrc').attr('src', $(this).attr('href'));
					window.parent.jQuery('#imagePreview').modal('show');
					return false;
				});
			});
		});
	"
);
?>
<form target="_parent" action="index.php?option=com_myportfolio&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="muted breadcrumbs">
		<p>
			<span class="icon-folder"></span>
			<?php
				echo $params->get($path, 'images'),
					($this->state->folder != '') ? '/' . $this->state->folder : '';
			?>
		</p>
	</div>

	<div>
		<label class="checkbox btn">
			<?php echo JHtml::_('grid.checkall'); ?>
			<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>
		</label>
	</div>

	<ul class="manager thumbnails thumbnails-media">
		<?php
			echo $this->loadTemplate('imgs');
		?>

		<input type="hidden" name="task" value="" />
        <input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
        <input type="hidden" name="catid" value="<?php echo $this->catid; ?>" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</ul>
</form>

<style>
    @import url(https://fonts.googleapis.com/css?family=Lato:300,400,700);
    .portBloq {
        margin-top: 70px;
        font-family: Lato;
    }

    .portBloq p {
        text-align: center;
    }
</style>
<div class="portBloq">
    <p><strong>Over 10 years without any support.., hours and weeks of code went into this update.</strong></p>
    <p><strong>I'll appreciate any support you can kindly provide, Thank you.</strong></p>
    <p><strong>Bitcoin: </strong>1KzgXcu9PqDnpGDTgMYULt9EbbgBJ6rN11</p>
    <p><strong>Ether: </strong>0x4f26809D94596AFb7294Cc7130F95335dC50700B</p>
</div>