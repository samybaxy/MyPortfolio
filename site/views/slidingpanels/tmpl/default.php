<?php
/**
 * MyPortfolio Sliding Panels Layout
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
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/modernizr.js");

//load CSS
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/reset.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/style.css");

$style = "";
if(count($this->data[1]) >= 4) {
    $imgCount = 1;
    foreach ($this->data[1] as $k => $v) {
        $img    = ''; $cat = $this->data[0];
        $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
        $JPath = JUri::root().'images/myportfolio/'.$this->data[0].'/'.$v->alias;
        if(JFolder::exists($path)) {
            $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
        }
        else {
            $img[0] = "noimage.jpg";
        }

        if ($k == 0) {
            $style .= "
                .cd-projects-previews a,
                .cd-projects .preview-image {
                  /* set a background image for each project */
                  background: #5b927d url($JPath/$img[0]) no-repeat center center;
                  background-size: cover;
                }
            ";

            $imgCount++;
        }
        else {
            $style .= "
                .cd-projects-previews li:nth-of-type($imgCount) a,
                .cd-projects > li:nth-of-type($imgCount) .preview-image {
                  background: #a8ae7e url($JPath/$img[0]) no-repeat center center;
                  background-size: cover;
                }
            ";

            $imgCount++;
        }

        if($imgCount == 5)
            break;
    }
}

$doc-> addStyleDeclaration($style);
?>
<a class="cd-nav-trigger cd-text-replace" href="#primary-nav">Menu<span aria-hidden="true" class="cd-icon"></span></a>

<div class="cd-projects-container">
    <ul class="cd-projects-previews">
        <?php if (count($this->data[1] >= 4)): ?>
        <?php $i = 0; foreach ($this->data[1] as $k => $v):
            if ( $i == 4 )
                break;
        ?>
        <li>
            <a href="#0">
                <div class="cd-project-title">
                    <h2><?php echo ucfirst(strtolower($v->project)); ?></h2>
                    <p><?php echo $v->short_description; ?></p>
                </div>
            </a>
        </li>
        <?php $i++; endforeach; endif; ?>
    </ul>

    <ul class="cd-projects">
        <?php if (count($this->data[1] >= 4)): ?>
        <?php $i = 0; foreach ($this->data[1] as $k => $v):
        if ( $i == 4 )
            break;
        ?>
        <li>
            <div class="preview-image">
                <div class="cd-project-title">
                    <h2><?php echo ucfirst(strtolower($v->project)); ?></h2>
                    <p><?php echo $v->short_description; ?></p>
                </div>
            </div>

            <div class="cd-project-info">
                <?php echo $v->description; ?>
            </div>
        </li>
        <?php $i++; endforeach; endif; ?>
        <!-- projects here -->
    </ul> <!-- .cd-projects -->

    <button class="scroll cd-text-replace">Scroll</button>
</div> <!-- .cd-project-container -->

<nav class="cd-primary-nav" id="primary-nav">
    <ul>
        <li class="cd-label">Navigation</li>
        <li>
             <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
        </li>
    </ul>
</nav>

<!-- Include jQuery -->
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery-2.1.1.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/main.js"; ?>"></script> <!-- Resource jQuery -->