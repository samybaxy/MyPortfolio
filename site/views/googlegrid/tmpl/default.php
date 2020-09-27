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

$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/imagesloaded.pkgd.min.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/masonry.pkgd.min.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/classie.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/modernizr.custom.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/cbpGridGallery.js");

//load CSS
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/googlegrid.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/component.css");

$script = <<<EOD
    jQuery(document).ready(function () {
        new CBPGridGallery( document.getElementById( 'grid-gallery' ));
    });
EOD;

$doc->addScriptDeclaration($script);
?>
<div id="grid-gallery" class="grid-gallery">
    <?php if($this->params->get('fullscreen')): ?>
        <div class="portHome">
            <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
        </div>
    <?php endif; ?>

    <section class="grid-wrap">
        <ul class="grid">
            <li class="grid-sizer"></li><!-- for Masonry column width -->
            <?php foreach($this->data[1] as $k => $v): ?>
                <?php
                    $img    = '';
                    $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
                    $JPath = JUri::root().'images/myportfolio/'.$this->data[0].'/'.$v->alias;
                    if(JFolder::exists($path)) {
                        $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                    }
                    else {
                        $img[0] = "noimage.jpg";
                    }
                ?>
            <li>
                <figure>
                    <img src="<?php echo $JPath.'/'.$img[0]; ?>" alt="<?php echo $v->alias; ?>" />
                    <figcaption><h3><?php echo $v->project; ?></h3><p><?php echo $v->short_description; ?></p></figcaption>
                </figure>
            </li>
            <?php endforeach; ?>
        </ul>
    </section><!-- // grid-wrap -->
    <section class="slideshow">
        <ul>
            <?php foreach($this->data[1] as $k => $v): ?>
                <?php
                    $img    = '';
                    $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
                    $JPath = JUri::root().'images/myportfolio/'.$this->data[0].'/'.$v->alias;
                    if(JFolder::exists($path)) {
                        $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                    }
                    else {
                        $img[0] = "noimage.jpg";
                    }
                ?>
            <li>
                <figure>
                    <figcaption>
                        <h3><?php echo $v->project; ?></h3>
                        <p><?php echo $v->description; ?></p>
                    </figcaption>
                    <img src="<?php echo $JPath.'/'.$img[0]; ?>" alt="<?php echo $v->alias; ?>" />
                </figure>
            </li>
            <?php endforeach; ?>
        </ul>
        <nav>
            <span class="icon nav-prev"></span>
            <span class="icon nav-next"></span>
            <span class="icon nav-close"></span>
        </nav>
        <div class="info-keys icon">Navigate with arrow keys</div>
    </section><!-- // slideshow -->
</div><!-- // grid-gallery -->