<?php
/**
 * MyPortfolio Horizon Site Layout
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
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/styles.css");

?>

<section id="portBloq">
    <?php if($this->params->get('fullscreen')): ?>
    <div class="portHome">
        <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
    </div>
    <?php endif; ?>
    <ul class="portfolio-items">
        <?php foreach ($this->data[1] as $k => $v):
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
        <li class="item">
            <figure>
                <div class="view"> <img src="<?php echo $JPath.'/'.$img[0]; ?>" /> </div>
                <figcaption>
                    <p>
                        <span>
                            <a href="<?php echo $v->url; ?>" target="_blank">
                                <?php echo ucfirst(strtolower($v->project)); ?>
                            </a>
                        </span>
                    </p>
                    <p><span><?php echo strip_tags($v->short_description); ?></span></p>
                </figcaption>
            </figure>
            <div class="date"><?php echo $v->date; ?></div>
        </li>
        <?php endforeach; ?>
    </ul>
    </section>

<!-- Include jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/modernizr-1.5.min.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery.mousewheel.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/scripts.js"; ?>"></script>