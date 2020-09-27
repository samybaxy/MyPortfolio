<?php
/**
 * MyPortfolio Filterizr Site Layout
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

//load CSS

$doc->addStyleSheet(JUri::base()."media/jui/css/bootstrap.css");
$doc->addStyleSheet(JUri::base()."media/jui/css/bootstrap-responsive.css");
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/index.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/styles.css");

?>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<section class="container showcase">
    <?php if($this->params->get('fullscreen')): ?>
        <div class="portHome">
            <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
        </div>
    <?php endif; ?>

    <div class="row">
        <ul class="nav nav-gallery filters simplefilter">
            <li class="filtr-button" data-filter="all">All</li>
            <?php $i = 1; ?>
            <?php foreach ($this->data[1] as $k => $v): ?>
                <li class="filtr-button" data-filter="<?php echo $i; ?>"><?php echo ucfirst(strtolower($v->project)); ?></li>
            <?php $i++; endforeach; ?>
        </ul>
        <div class="">
            <button class="filtr-button filtr-shuffle" data-shuffle>Shuffle</button>
            <ul class="nav nav-gallery sorting sortandshuffle">
                <li class="filtr-button filtr-sort filtr active" data-sortasc>Asc</li>
                <li class="filtr-button filtr-sort filtr" data-sortdesc>Desc</li>
            </ul>
            <input type="text" name="filtr-search" class="filtr-search" value="" placeholder="Your search" data-search="">
        </div>
    </div>

    <div class="row">
        <div class="filtr-container">
            <?php $i = 1; ?>
            <?php foreach($this->data[1] as $k => $v): ?>
            <div class="filtr-item" data-category="<?php echo $i; ?>" data-sort="<?php echo $v->project; ?>">
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
                <img class="img-responsive" src="<?php echo $JPath.'/'.$img[0]; ?> " alt="sample">
                <span class="item-desc"><?php echo $v->project; ?></span>
            </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
</section>

<!-- Include jQuery & Filterizr -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/filterizr/jquery.filterizr.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/controls.js"; ?>"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(function() {
            //Initialize filterizr with default options
            jQuery(".filtr-container").filterizr();
        });
    })
</script>