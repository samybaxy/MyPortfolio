<?php
/**
 * MyPortfolio Default Frontend layout
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

$jPath = JUri::base();

//Load Jquery library
$doc = JFactory::getDocument();

//load JS
JHtml::_('jquery.framework');
$doc->addScript("{$jPath}components/com_myportfolio/views/virgo/assets/js/easing.js");
$doc->addScript("{$jPath}components/com_myportfolio/views/virgo/assets/js/slides.min.jquery.js");

//load CSS
$doc->addStyleSheet ($jPath.'components/com_myportfolio/views/virgo/assets/css/global.css');
$doc->addStyleSheet ($jPath.'components/com_myportfolio/views/virgo/assets/css/standard.css');
$doc->addStyleSheet ($jPath.'components/com_myportfolio/views/virgo/assets/css/font-awesome.min.css');

//Dimensions for template
$lwidth = $this->params->get('lwidth');
if(!$lwidth) {
    $lwidth = '600';
}
$lwidth = str_replace('px', '', $lwidth);
if($lwidth < 600) {
    $lwidth = '600';
}

$lheight = $this->params->get('lheight');
if(!$lheight) {
    $lheight = '300';
}
$lheight = str_replace('px', '', $lheight);
if($lheight < 300) {
    $lheight = '300';
}

$twidth = $this->params->get('twidth');
if(!$twidth) {
    $twidth = '90px';
}
$theight = $this->params->get('theight');
if(!$theight) {
    $theight = '90px';
}

//param for new ribbon image
$ribbon = $this->params->get('ribbon');

// embedded style for easyslider plugin
$css_code = "
#example {
    margin-bottom: 45px;
    position:relative;
}

#slides img {
    width: {$lwidth}px!important;
    height: {$lheight}px!important;    	
}
 
p.client span {
    font-weight: 600;
}

body {
  -webkit-font-smoothing: antialiased;
  font: normal 15px/1.5 \"Helvetica Neue\", Helvetica, Arial, sans-serif;
  color: #232525;
}

#slides {
  display: none;
}

#slides .slidesjs-navigation {
  margin-top:3px;
}

#slides .slidesjs-previous {
  margin-right: 5px;
  float: left;
}

#slides .slidesjs-next {
  margin-right: 5px;
  float: left;
}

.slidesjs-pagination {
  margin: 6px 0 0;
  float: right;
  list-style: none;
}

.slidesjs-pagination li {
  float: left;
  margin: 0 1px;
}

.slidesjs-pagination li a.active,
.slidesjs-pagination li a:hover.active {
  background-position: 0 -13px
}

.slidesjs-pagination li a:hover {
  background-position: 0 -26px
}

#slides a:link,
#slides a:visited {
  color: #fff
}

#slides a:hover,
#slides a:active {
  color: #9e2020
}

.navbar {
  overflow: hidden
}
<!-- End SlidesJS Optional-->

<!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
#slides {
  display: none;
}

.container {
  margin: 0 auto
}

/* For tablets & smart phones */
@media (max-width: 767px) {
  body {
    padding-left: 20px;
    padding-right: 20px;
  }
  .container {
    width: auto
  }
}

/* For smartphones */
@media (max-width: 480px) {
  .container {
    width: auto
  }
}

/* For smaller displays like laptops */
@media (min-width: 768px) and (max-width: 979px) {
  .container {
    width: 724px
  }
}

/* For larger displays */
@media (min-width: 1200px) {
  .container {
    width: 1170px
  }
}
<!-- SlidesJS Required: -->
";

$doc->addStyleDeclaration($css_code);

$ajaxx = <<<EOD
jQuery.noConflict();
   jQuery(document).ready(function() {
        jQuery('#preloader').hide();

        jQuery('.ftproj').click(function(e) {
            e.preventDefault();			                
            jQuery('#preloader').fadeIn( 1000 );			                
            jQuery('#myContentLoad').fadeOut( 500 );
                                        
            var url = jQuery(this).attr('href');		                
            jQuery.post(url).success( function(data) {
                    setTimeout(function () {
                        jQuery('#preloader').fadeOut( 500 );
                        jQuery('#myContentLoad').html( data );
                        jQuery('#myContentLoad').fadeIn( 1000 );
                        				                    
                        jQuery(function(){
                        jQuery("#slides").slidesjs({
                            width: $lwidth,
                            height: $lheight,
                            preload: true,
                            preloadImage: "components/com_myportfolio/views/virgo/assets/images/loading.gif",
                            play: 5000,
                            pause: 2500,
                            hoverPause: true,
                            navigation: {
                                active: false,
                                effect: "fade"
                            }
                        });
                    });
                    }, 1500);
                })
                .error(function(data) {
                    alert("No project defined under portfolio category");
                });
            return false;
    });
});
EOD;
$doc->addScriptDeclaration( $ajaxx ); ?>
<div id="myPortfolio">
    <h1><?php echo $this->data[0]->name; ?></h1>
    <div id="preContentLoad">
        <div id="preloader">
            <div class="preImg">
                <img src="<?php echo 'components/com_myportfolio/views/virgo/assets/images/ajax-loader.gif'; ?>" alt="loader image" />
            </div>
        </div>
        <div id="myContentLoad">
            <div id="newHope" class="span8">
                <div id="example">
                    <?php if($ribbon == 1): ?>
                        <img src="<?php echo $jPath;?>components/com_myportfolio/views/virgo/assets/images/new-ribbon.png" width="112" height="112" alt="New Ribbon" id="ribbon">
                    <?php endif; ?>
                    <div id="slides">
                        <?php
                            $v = $this->data[1];
                            $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0]->alias.DS.$v->alias;
                            $JPath = 'images/myportfolio/'.$this->data[0]->alias.'/'.$v->alias;
                            if(JFolder::exists($path)) {
                                $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                            }
                            else {
                                $img[0] = "noimage.jpg";
                            }
                            foreach ($img as $image) : ?>
                                <img src="<?php echo $JPath.'/'.$image; ?>"  width="" height="" />
                        <?php endforeach; ?>
                        <a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
                        <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
                    </div>
                    <img src="<?php echo $jPath;?>components/com_myportfolio/views/virgo/assets/images/example-frame.png" width="<?php echo $lwidth; ?>" height="<?php echo $lheight; ?>" alt="Example Frame" id="whiteframe">
                </div>
            </div>
            <div class="flitems span4">
                <h4><?php echo $this->data[1]->project; ?></h4>
                <p class="client"><span><?php echo JText::_('COM_MYPORTFOLIO_CLIENT'); ?></span> <?php echo $this->data[1]->client; ?></p>
                <div><?php echo $this->data[1]->short_description; ?></div>
                <a href="<?php echo $this->data[1]->url;?>" target="_blank" style="color: #08c!important; :hover: #005580;"><?php echo $this->data[1]->url;?></a>
            </div>
        </div>
        <div id='portListNoS'>
            <h3><?php echo JText::_('COM_MYPORTFOLIO_PROJECT'); ?></h3>
                <ul class="windowList">
                    <?php
                        foreach($this->pList[1] as $pList):
                            $link = JRoute::_( 'index.php?task=project&view=virgo&pid='.$pList->id .'&format=raw' );
                            $catAlias = str_replace(' ', '-', $this->data[0]->alias);
                    ?>
                    <li>
                        <ul class="secList">
                        <li>
                            <a href="<?php echo $link; ?>" class='ftproj' target="_blank" title="<?php echo $pList->alias; ?>" style="display: block; padding: 3px; border: 1px solid #ccc!important; overflow: hidden; width: <?php echo $twidth; ?>; height: <?php echo $theight; ?>;">
                                <?php
                                    $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$catAlias.DS.$pList->alias;
                                    $JPath = 'images/myportfolio/'.$catAlias.'/'.$pList->alias;
                                    if(JFolder::exists($path)) {
                                        $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                                    }
                                    else {
                                        $img[0] = "noimage.jpg";
                                    }
                                ?>
                                <img src="<?php echo $jPath.'images/myportfolio/'.$catAlias.'/'.$pList->alias.'/'.$img[0]; ?>"
                                 alt="" width="<?php //echo $twidth; ?>" height="<?php //echo $theight; ?>" style="width: <?php echo $twidth; ?>; height: <?php echo $theight; ?>;" />
                            </a>
                        </li>
                        <li><a href="<?php echo $link; ?>" class='ftproj'><?php echo ucfirst($pList->project); ?></a></li>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
        </div>
    </div>
</div>

<script>
    jQuery.noConflict();
    jQuery(function(){
        jQuery("#slides").slidesjs({
            width: <?php echo $lwidth; ?>,
            height: <?php echo $lheight; ?>,
            preload: true,
            preloadImage: "components/com_myportfolio/views/virgo/assets/images/loading.gif",
            play: 5000,
            pause: 2500,
            hoverPause: true,
            navigation: {
                active: false,
                effect: "fade"
            }
        });
    });
</script>