<?php
/**
 * MyPortfolio Administrator Categories layout
 *
 * @package		MyPortfolio.Administrator
 * @subpackage	com_myportfolio
 * @author		samybaxy
 * @copyright   Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link		https://www.samybaxy.net
 * @license		GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering   = ($listOrder == 'a.ordering');
$saveOrder  = ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');
$loggeduser = JFactory::getUser();

$document   = JFactory::getDocument();
$document->addStyleSheet(juri::root().'media/com_myportfolio/css/admin.myportfolio.css');
?>

<form action="<?php echo JRoute::_('index.php?option=com_myportfolio&view=categories', false); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));

        if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-hover table-striped" id="categoryList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th width="1%">
                            <?php echo JHtml::_('grid.checkall'); ?>
                        </th>
                        <th width="1%">
                            <?php echo JHtml::_('searchtools.sort', 'COM_MYPORTFOLIO_STATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th class="center" width="10%">
                            <?php echo JHtml::_('searchtools.sort', 'COM_MYPORTFOLIO_MANAGE_PROJECTS', '', $listDirn, $listOrder); ?>
                        </th>
                        <th class="nowrap text-left">
                            <?php echo JHtml::_('searchtools.sort', 'COM_MYPORTFOLIO_CATEGORY_NAME', 'a.name', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%" class="nowrap center">
                            <?php echo JHtml::_('searchtools.sort',  'COM_MYPORTFOLIO_CATEGORY_PROJECT_COUNT', 'count', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone center">
                            <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%" class="nowrap center">
                            <?php echo JHtml::_('searchtools.sort', 'COM_MYPORTFOLIO_HITS', 'a.hits', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="9">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                        foreach ($this->items as $i => $item) :
                            $orderkey   = $item->ordering;
                            $canEdit    = $this->canDo->get('core.edit');
                            $canChange  = $loggeduser->authorise('core.edit.state',	'com_myportfolio');

                            // If this group is super admin and this user is not super admin, $canEdit is false
                            if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin'))
                            {
                                $canEdit   = false;
                                $canChange = false;
                            }

                            $parentsStr = '';
                    ?>

                    <tr class="row<?php echo $i % 2; ?>" sortable-group-id="0" item-id="<?php echo $item->id; ?>" parents="<?php echo $parentsStr; ?>" level="0">
                        <td class="order nowrap center hidden-phone">
                            <?php
                            $iconClass = '';

                            if (!$canChange)
                            {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder)
                            {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
                                <span class="icon-menu" aria-hidden="true"></span>
                            </span>
                            <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="ordering[]" size="5" value="<?php echo $orderkey + 1; ?>" />
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($canEdit || $canChange) : ?>
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $published =  $item->state;
                            echo JHtml::_('jgrid.published', $published, $i, 'categories.', $canChange, 'cb'); ?>
                        </td>
                        <td class="center">
                            <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.(int) $item->id); ?>" class="btn btn-success">
                                    <?php echo  JText::_('COM_MYPORTFOLIO_MANAGE'); ?>
                                </a>
                            <?php else : ?>
                                <?php echo  JText::_('COM_MYPORTFOLIO_MANAGE_UNAUTHORIZED'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="name break-word">
                                <strong class="small">Edit</strong>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_myportfolio&task=category.edit&id=' . (int) $item->id); ?>" class="btn btn-warning" title="<?php echo JText::sprintf('COM_MYPORTFOLIO_EDIT_CATEGORY', $this->escape($item->name)); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>
                                <p class="small">
                                    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                                </p>
                            </div>
                        </td>

                        <td class="small center">
                            <?php echo $this->escape($item->count); ?>
                        </td>
                        <td class="small center">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="center"><?php echo $item->hits; ?></td>
                        <td class="center"><?php echo $item->id; ?></td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
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