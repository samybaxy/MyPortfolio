<?php
/**
 * MyPortfolio Google Grid Site Layout
 *
 * @package	      MyPortfolio.Site
 * @subpackage	  com_myportfolio
 * @author		  samybaxy
 * @copyright     Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
 *
 * @link          https://www.samybaxy.net
 * @license	      GNU/GPLv3
 */

// no direct access
defined( '_JEXEC' ) or die;
if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

//Load Js
$doc = JFactory::getDocument();

JHtml::_('jquery.framework');
$doc->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/plugins.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/scripts.js");

//load CSS
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/gallery-styles.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/styles.css");

$script = <<<EOD
    jQuery.noConflict();
    jQuery(document).ready(function(){
        jQuery('#gallery-container').sGallery({
            fullScreenEnabled: true //default is false
        });
    });
EOD;

$doc->addScriptDeclaration($script);
?>

<section id="portBloq">
    <?php if($this->params->get('fullscreen')): ?>
        <div class="portHome">
            <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
        </div>
    <?php endif; ?>
    <div id="gallery-container">
        <ul class="items--small">
            <?php
                $img    = '';
                $cat    = $this->data[0]->alias;
                $project= $this->data[1];
                $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$cat.DS.$project->alias;
                $JPath  = JUri::root().'images/myportfolio/'.$cat.'/'.$project->alias;
                if(JFolder::exists($path)) {
                    $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                }
            ?>
            <?php if (!empty($img)): ?>
                <?php foreach ($img as $image): ?>
                    <li class="item">
                        <a href="javascript:void(0);">
                            <img src="<?php echo $JPath.'/'.$image; ?>" alt="<?php echo $project->alias; ?>" />
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <ul class="items--big">
            <?php
                $img    = '';
                $cat    = $this->data[0]->alias;
                $project= $this->data[1];
                $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$cat.DS.$project->alias;
                $JPath = JUri::root().'images/myportfolio/'.$cat.'/'.$project->alias;
                if(JFolder::exists($path)) {
                    $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                }
            ?>
            <?php if (!empty($img)): ?>
                <?php foreach ($img as $image): ?>
                    <li class="item--big">
                        <a href="javascript:void(0);">
                            <figure>
                                <img src="<?php echo $JPath.'/'.$image; ?>" alt="<?php echo $project->alias; ?>" />
                                <figcaption class="img-caption">
                                    <?php echo $project->short_description; ?>
                                </figcaption>
                            </figure>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <div class="controls">
            <span class="control icon-arrow-left" data-direction="previous"></span>
            <span class="control icon-arrow-right" data-direction="next"></span>
            <span class="grid icon-grid"></span>
            <span class="fs-toggle icon-fullscreen"></span>
        </div>
    </div>
</section>