<?php
/**
 * MyPortfolio Administrator portfolio upload layout
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

$basepath= JPATH_SITE.DS."images";

$mediascript = <<<EOF
    var basepath = '$basepath';
    var viewstyle = 'thumbs';
    
    jQuery(document).ready(function($) {
        $('#imagePreview').on('show.bs.modal', function() {
            $('body').addClass('modal-open');
            $('.modalTooltip').each(function(){;
                var attr = $(this).attr('data-placement');
                if ( attr === undefined || attr === false ) $(this).attr('data-placement', 'auto-dir top-left')
           });
            $('.modalTooltip').tooltip({'html': true, 'container': '#imagePreview'});
       }).on('shown.bs.modal', function() {
            var modalHeight = $('div.modal:visible').outerHeight(true),
               modalHeaderHeight = $('div.modal-header:visible').outerHeight(true),
               modalBodyHeightOuter = $('div.modal-body:visible').outerHeight(true),
               modalBodyHeight = $('div.modal-body:visible').height(),
               modalFooterHeight = $('div.modal-footer:visible').outerHeight(true),
               padding = document.getElementById('imagePreview').offsetTop,
               maxModalHeight = ($(window).height()-(padding*2)),
               modalBodyPadding = (modalBodyHeightOuter-modalBodyHeight),
               maxModalBodyHeight = maxModalHeight-(modalHeaderHeight+modalFooterHeight+modalBodyPadding);
           if (modalHeight > maxModalHeight){;
               $('.modal-body').css({'max-height': maxModalBodyHeight, 'overflow-y': 'auto'});
           }
       }).on('hide.bs.modal', function () {
            $('body').removeClass('modal-open');
            $('.modal-body').css({'max-height': 'initial', 'overflow-y': 'initial'});
           $('.modalTooltip').tooltip('destroy');
       });
    });
    
EOF;

JFactory::getDocument()->addScriptDeclaration($mediascript);

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.framework');
JHtml::_('script', 'com_myportfolio/mediamanager.min.js', array('version' => 'auto', 'relative' => true));

$doc		= JFactory::getDocument();
$loggeduser	= JFactory::getUser();
$userId		= $loggeduser->get('id');

$folder = "myportfolio/{$this->cat}/{$this->project}";
$mfolder= "myportfolio/{$this->cat}/{$this->project}";

if(empty($this->imgDirectory)) {
    $session = JFactory::getSession();
    $this->imgDirectory = $session->get('pathToFiles');
}

$images         = glob($this->imgDirectory.DS."*.*");
$supported_exts = array(
    'gif','jpg','jpeg','png'
);

//get cat id from request
$jinput = JFactory::getApplication()->input;
$pid = $jinput->get('pid', null, 'int');

if(empty($pid)) {
    $pid = $jinput->post->get('pid', null, 'int');
}

//get cat id from request
$catid = $jinput->get('catid', null, 'int');
if(empty($catid)) {
    $catid = $jinput->post->get('catid', null, 'int');
}

$session = JFactory::getSession();
$session->set('pid', $pid);
$session->set('catid', $catid);

$doc->addStyleSheet(juri::root().'media/com_myportfolio/css/admin.myportfolio.css');

if(isset($this->items[0]->project)) {
    $check = $this->items[0]->project;
} else {
    $check = '';
}

?>

<div class="row-fluid">
    <?php if(!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
     <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
        <form action="<?php echo JRoute::_(JUri::root()."administrator/index.php?option=com_myportfolio&task=images.upload&tmpl=component&format=html&folder={$folder}&{$this->session->getName()}={$this->session->getId()}&".JSession::getFormToken()."=1", false); ?>" id="uploadForm" class="form-inline" name="uploadForm" method="post" enctype="multipart/form-data">
            <div id="uploadform" class="uploadform">
                <fieldset id="upload-noflash" class="actions">
                    <label for="upload-file" class="control-label">Upload file</label>
                    <input required="" type="file" id="upload-file" name="Filedata[]" multiple=""> <button class="btn btn-primary" id="upload-submit"><span class="icon-upload icon-white"></span> Start Upload</button>
                    <p class="help-block">
                        <?php $cMax    = (int) $this->config->get('upload_maxsize'); ?>
                        <?php $maxSize = JUtility::getMaxUploadSize($cMax . 'MB'); ?>
                        <?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::_('number.bytes', $maxSize)); ?>
                    </p>
                </fieldset>
                <input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $folder; ?>">
                <?php JFactory::getSession()->set("com_myportfolio.return_url", "index.php?option=com_myportfolio&view=images&pid={$pid}&catid={$catid}"); ?>
            </div>
        </form>

        <form action="index.php?option=com_myportfolio" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="cb1" id="cb1" value="0" />
            <input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $folder; ?>" />
        </form>

        <form action="<?php JRoute::_("index.php?option=com_myportfolio&task=images.createfolder&tmpl={$jinput->getCmd('tmpl', 'component')}", false) ?>" name="folderForm" id="folderForm" method="post">
            <div id="folderview">
                <div class="view">
                    <iframe class="thumbnail" src="index.php?option=com_myportfolio&view=mediaList&tmpl=component&folder=<?php echo $mfolder; ?>" id="folderframe" name="folderframe" width="100%" height="500px" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
                </div>
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
    </div>
        <form action="<?php echo JRoute::_('index.php?option=com_myportfolio&view=projects&catid='.$catid, false); ?>" method="post" name="adminForm" id="adminForm">
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="catid" value="<?php echo $catid; ?>" />
        </form>
</div>
<?php // Pre render all the bootstrap modals on the parent window

echo JHtml::_(
    'bootstrap.renderModal',
    'imagePreview',
    array(
        'title'  => JText::_('COM_MYPORTFOLIO_PREVIEW'),
        'footer' => '<a type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
            . JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
    ),
    '<div id="image" style="text-align:center;"><img id="imagePreviewSrc" src="../media/jui/img/alpha.png" alt="preview" style="max-width:100%; max-height:300px;"/></div>'
);
?>

<script type="text/javascript">
    jQuery(document).ready(function() {
       var url = "<?php echo JUri::root()."administrator/index.php?option=com_myportfolio&view=projects&catid={$catid}"; ?>";
       jQuery("#submenu > li > a").attr("href", url);
    });
 </script>