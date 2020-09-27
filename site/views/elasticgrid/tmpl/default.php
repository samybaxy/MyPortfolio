<?php
/**
 * MyPortfolio Elastic Grid Site Layout
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
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/elastic.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/elastic_grid.min.css");
?>

<div id="elastic_grid_demo"></div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/modernizr.custom.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/classie.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery.elastislide.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery.hoverdir.js"; ?>"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/elastic_grid.js"; ?>"></script>
<?php
$options = "";
foreach($this->data[1] as $k => $v) {
    $img        = array();
    $thumbnails = array();
    $alias      = array();
    $stringalias= array();

    $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
    $JPath = 'images/myportfolio/'.$this->data[0].'/'.$v->alias;
    if(JFolder::exists($path)) {
        $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
    }
    else {
        $img[0] = "noimage.jpg";
    }

    foreach ($img as $image) {
        $thumbnails[]   = $JPath.'/'.$image;
        $alias[]        = ucfirst(strtolower($v->project));
    }

    $desc = ($v->short_description) ? rtrim(htmlspecialchars_decode(preg_replace('/\s+/', ' ', strip_tags($v->short_description)), ENT_HTML5)) : "";

    $thumbs     = "'" . implode ( "', '", $thumbnails ) . "'";
    $stringalias= "'" . implode ( "', '", $alias ) . "'";
    $options    .= "
                {
                    'title'         : '".ucfirst(strtolower($v->project))."',
                    'description'   : '".$desc."',
                    'thumbnail'     : [$thumbs],
                    'large'         : [$thumbs],
                    'img_title'     : [$stringalias],
                    'button_list'   :
                    [
                        { 'title':'Demo', 'url' : '$v->url', 'new_window' : true }
                    ],
                    'tags'          : ['Self Portrait']
                },
    ";
}

$script = <<<EOD
jQuery(document).ready(function($) {
    $(function() {
        $("#elastic_grid_demo").elastic_grid({
            'showAllText' : 'All',
            'filterEffect': 'popup', // moveup, scaleup, fallperspective, fly, flip, helix , popup
            'hoverDirection': true,
            'hoverDelay': 0,
            'hoverInverse': false,
            'expandingSpeed': 500,
            'expandingHeight': 500,
            'items' :
            [
                $options    
            ]
        });
    });
});
EOD;

if($this->params->get('fullscreen')): ?>
    <div class="portHome">
        <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
    </div>
<?php endif; ?>

<script>
    <?php echo $script; ?>
</script>
