<?php
/**
 * MyPortfolio Split Site Layout
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

$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/modernizr.custom.js");
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/classie.js", array(), array('defer' => 'defer'));
$doc->addScript(JUri::base()."components/com_myportfolio/views/$this->view/assets/js/cbpSplitLayout.js", array(), array('defer' => 'defer'));

//load CSS
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/split.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/component.css");

$category = trim($this->data[0]);
$project1 = $this->data[1][0];
$project2 = $this->data[1][1];

?>
<div class="container">
    <div id="splitlayout" class="splitlayout">
        <div class="intro">
            <div class="side side-left">
                <?php if($this->params->get('fullscreen')): ?>
                    <div class="portHome">
                        <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
                    </div>
                <?php endif; ?>
                <div class="intro-content">
                    <?php
                        $img    = '';
                        $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$category.DS.$project1->alias;
                        $JPath = JUri::root().'images/myportfolio/'.$category.'/'.$project1->alias;
                        if(JFolder::exists($path)) {
                            $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                        }
                        else {
                            $img[0] = "noimage.jpg";
                        }
                    ?>
                    <div class="profile"><img src="<?php echo $JPath.'/'.$img[0]; ?>" alt="<?php echo $project1->alias; ?>"></div>
                    <h1><span><?php echo ucfirst($project1->project); ?></span>
                        <span>
                            <?php if (!empty($project1->url)): ?>
                                <a href="<?php echo $project1->url; ?>" target="_blank"><?php echo strtolower($project1->url); ?></a>
                            <?php endif; ?>
                        </span>
                    </h1>
                </div>
                <div class="overlay"></div>
            </div>
            <div class="side side-right">
                <div class="intro-content">
                    <?php
                        $img    = '';
                        $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$category.DS.$project2->alias;
                        $JPath = JUri::root().'images/myportfolio/'.$category.'/'.$project2->alias;
                        if(JFolder::exists($path)) {
                            $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                        }
                        else {
                            $img[0] = "noimage.jpg";
                        }
                    ?>
                    <div class="profile"><img src="<?php echo $JPath.'/'.$img[0]; ?>" alt="<?php echo $project2->alias; ?>"></div>
                    <h1><span><?php echo ucfirst($project2->project); ?></span>
                        <span>
                            <?php if (!empty($project2->url)): ?>
                                <a href="<?php echo $project2->url; ?>" target="_blank"><?php echo strtolower($project2->url); ?></a>
                            <?php endif; ?>
                        </span>
                    </h1>
                </div>
                <div class="overlay"></div>
            </div>
        </div><!-- /intro -->
        <div class="page page-right page-large">
            <div class="page-inner">
                <?php echo $project2->description ?>
                <!-- content -->
            </div><!-- /page-inner -->
        </div><!-- /page-right -->
        <div class="page page-left page-fill">
            <div class="page-inner">
                <?php echo $project1->description ?>
                <!-- content -->
            </div><!-- /page-inner -->
        </div><!-- /page-left -->
        <a href="#" class="back back-right" title="back to intro">→</a>
        <a href="#" class="back back-left" title="back to intro">←</a>
    </div><!-- /splitlayout -->
</div>