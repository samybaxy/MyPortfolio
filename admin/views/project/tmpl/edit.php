<?php
/**
 * MyPortfolio Administrator project edit form
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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$document   = JFactory::getDocument();
$document->addStyleSheet(juri::root().'media/com_myportfolio/css/admin.myportfolio.css');

$jinput = JFactory::getApplication()->input;
$catid = $jinput->getInt('catid');
if(empty($catid)) {
    $catid = $jinput->post->get('catid');
}

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'project.cancel' || document.formvalidator.isValid(document.id('project-form'))) {
			" . $this->form->getField('short_description')->save() . "
			" . $this->form->getField('description')->save() . "
			Joomla.submitform(task, document.getElementById('project-form'));
		}
	}");
?>

<form action="<?php echo JRoute::_('index.php?option=com_myportfolio&layout=edit&id='.(int)$this->item->id).'&catid='.$catid; ?>" method="post" name="adminForm" id="project-form" class="form-validate">
    <div class="form-vertical">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_MYPORTFOLIO_NEW_PROJECT') : JText::sprintf('COM_MYPORTFOLIO_EDIT_PROJECT')); ?>
        <div class="row-fluid">
            <div class="span9">
                <div class="form-vertical">
                    <?php echo $this->form->renderField('project'); ?>
                    <?php echo $this->form->renderField('date'); ?>
                    <?php echo $this->form->renderField('duration'); ?>
                    <?php echo $this->form->renderField('client'); ?>
                    <?php echo $this->form->renderField('url'); ?>
                    <?php echo $this->form->renderField('short_description'); ?>
                    <?php echo $this->form->renderField('description'); ?>
                </div>
            </div>
            <div class="span3">
                <?php echo $this->form->renderField('catid'); ?>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_alias-lbl" for="jform_alias" class="hasPopover" title="" data-content="Automatically generated from category name if left empty" data-original-title="Alias">
                            <strong><small>Alias</small></strong>
                        </label>
                    </div>
                    <?php echo $this->form->getInput('alias'); ?>
                </div>
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span12">
                <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</div>
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