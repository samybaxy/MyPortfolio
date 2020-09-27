<?php
/**
 * MyPortfolio Administrator project layout
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
$saveOrder	= ($listOrder == 'a.ordering' && strtolower($listDirn) == 'asc');
$loggeduser = JFactory::getUser();
$document   = JFactory::getDocument();
$session    = JFactory::getSession();
$document->addStyleSheet(juri::root().'media/com_myportfolio/css/admin.myportfolio.css');

//get category id from request
$jinput = JFactory::getApplication()->input;
$catid  = $jinput->get('catid', null, 'int');
$session->set('catid', $catid);
?>

<form action="<?php echo JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false); ?>" method="post" name="adminForm" id="adminForm">
    <?php if(!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
    <?php endif;?>

        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));

        if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-hover table-striped" id="projectList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th width="1%">
                            <?php echo JHtml::_('grid.checkall'); ?>
                        </th>
                        <th width="1%">
                            <?php echo JHTML::_( 'searchtools.sort', JText::_('COM_MYPORTFOLIO_FEATURED' ), 'a.default', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="1%" class="center">
                            <?php echo JHTML::_('searchtools.sort', JText::_('COM_MYPORTFOLIO_STATUS'), 'a.state', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="5%" class="center">
                            <?php echo JHTML::_( 'searchtools.sort', JText::_( 'COM_MYPORTFOLIO_IMAGES' ), '', $listDirn, $listOrder ); ?>
                        </th>
                        <th class="nowrap text-left">
                            <?php echo JHTML::_( 'searchtools.sort', JText::_('COM_MYPORTFOLIO_PROJECT_NAME' ), 'a.project', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="5%" class="nowrap  center">
                            <?php echo JHtml::_('searchtools.sort',  'COM_MYPORTFOLIO_CATEGORY_NAME', 'n.category', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%" class="nowrap hidden-phone center">
                            <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone center">
                            <?php echo JHTML::_('searchtools.sort', 'COM_MYPORTFOLIO_DATE_LAUNCHED', 'a.date', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone center">
                            <?php echo JHTML::_('searchtools.sort', 'COM_MYPORTFOLIO_DURATION', 'a.duration', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="10%" class="center hidden-phone">
                            <?php echo JHTML::_('searchtools.sort', 'COM_MYPORTFOLIO_CLIENT', 'a.client', $listDirn, $listOrder ); ?>
                        </th>
                        <th width="1%" class="nowrap center">
                            <?php echo JHTML::_('searchtools.sort', JText::_( 'COM_MYPORTFOLIO_HITS' ), 'a.hits', $listDirn, $listOrder );	?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone center">
                            <?php echo JHTML::_('searchtools.sort', JText::_( 'ID' ), 'a.id', $listDirn, $listOrder ); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="13">
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
                            $canCheckin	= $loggeduser->authorise('core.manage','com_checkin') || $item->checked_out==$loggeduser->get('id') || $item->checked_out==0;
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
                        <td class="center">
                            <?php if ($canEdit): ?>
                                <?php
                                    if ($item->default == 1) {
                                        echo '<img src="' .JURI::root().'/media/com_myportfolio/images/default.png" />';
                                    }
                                ?>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <?php
                            $published =  $item->state;
                            echo JHtml::_('jgrid.published', $published, $i, 'projects.', $canChange, 'cb'); ?>
                        </td>
                        <td class="center">
                            <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_myportfolio&view=images&pid='.(int) $item->id.'&catid='.$catid); ?>" class="btn btn-success">
                                    <?php echo  JText::_('COM_MYPORTFOLIO_MANAGE_IMAGES'); ?>
                                </a>
                            <?php else : ?>
                                <span class="small">
                                    <?php echo  JText::_('COM_MYPORTFOLIO_NO_MANAGE_IMAGES'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="small">Edit</strong>
                            <?php if ($canEdit): ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_myportfolio&task=project.edit&id='.(int) $item->id.'&catid='.$catid); ?>" class="btn btn-warning">
                                    <?php echo $this->escape($item->project); ?>
                                </a>
                            <?php else : ?>
                                <?php echo $this->escape($item->project); ?>
                            <?php endif; ?>
                            <p class="small">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                            </p>
                        </td>
                        <td class="center">
                            <strong>
                                <?php echo $this->escape($item->category); ?>
                            </strong>
                        </td>
                        <td class="small center">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="center">
                            <?php echo $item->date; ?>
                        </td>
                        <td class="center">
                            <?php echo $item->duration; ?>
                        </td>
                        <td class="center">
                            <?php echo $item->client; ?>
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
    <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
    <?php echo JHTML::_('form.token');?>
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