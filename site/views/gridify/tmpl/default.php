<?php
/**
 * MyPortfolio Gridify Site Layout
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
JHtml::_('bootstrap.framework');

//load CSS
$doc->addStyleSheet("//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/gridify.css");
$doc->addStyleSheet(JUri::base()."components/com_myportfolio/views/$this->view/assets/css/jquery.fancybox.min.css");

//baseurl
$baseUrl = JUri::base()."components/com_myportfolio/views/$this->view/assets/js/";
?>
<div id="loading">
    <img src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/img/loading.gif"; ?>" />
</div>
<div class="grid" style="display: none;">
    <?php if($this->params->get('fullscreen')): ?>
        <div class="portHome">
            <a href="<?php echo JUri::base(); ?>"><span class="fa fa-home"></span></a>
        </div>
    <?php endif;
    if (!empty($this->data)) : ?>
        <?php foreach ($this->data[1] as $k => $v): ?>
            <a href="Javascript:void(0);" class="fancy" data-id="<?php echo $k; ?>">
                <?php
                    $img    = '';
                    $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
                    $JPath = JUri::base().'images/myportfolio/'.$this->data[0].'/'.$v->alias;
                    if(JFolder::exists($path)) {
                        $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                    }
                    else {
                        $img[0] = "noimage.jpg";
                    }
                ?>
                <img src="<?php echo $JPath.'/'.$img[0]; ?>" alt="<?php echo $v->alias; ?>">
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div><!-- // gridify -->

<?php foreach ($this->data[1] as $k => $v): ?>
    <div style="display: none;" id="fancybox<?php echo $k; ?>">
        <?php
            $img    = '';
            $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0].DS.$v->alias;
            $JPath  = JUri::base().'images/myportfolio/'.$this->data[0].'/'.$v->alias;
            if(JFolder::exists($path)) {
                $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
            }
            else {
                $img[0] = "noimage.jpg";
            }
        ?>
        <?php foreach ($img as $image): ?>
            <a class="gallery" data-fancybox="gallery<?php echo $k; ?>" href="<?php echo $JPath.'/'.$image; ?>" data-caption="<?php echo $v->short_description; ?>">
                <img src="<?php echo $JPath.'/'.$image; ?>">
            </a>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/require.js"; ?>" type="text/javascript"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery-3.2.1.min.js"; ?>" type="text/javascript"></script>
<script src="<?php echo JUri::base()."components/com_myportfolio/views/$this->view/assets/js/jquery.fancybox.js"; ?>" type="text/javascript"></script>

<script>
    setTimeout(function() {
        jQuery("#loading").fadeOut();
        jQuery(".grid").fadeIn("slow");
        require.config({
            baseUrl: "<?php echo $baseUrl; ?>",
            paths: {
                jquery: 'jquery-1.11.1.min',
                gridify: 'gridify-min'
            }
        });

        require( ["jquery", "gridify"],
            function($) {
                var options =
                    {
                        srcNode: 'img',             // grid items (class, node)
                        margin: '20px',             // margin in pixel, default: 0px
                        width: '250px',             // grid item width in pixel, default: 220px
                        max_width: '',              // dynamic gird item width if specified, (pixel)
                        resizable: true,            // re-layout if window resize
                        transition: 'all 0.5s ease' // support transition for CSS3, default: all 0.5s ease
                    }

                jQuery('.grid').gridify(options);
            }
        );

        jQuery(".fancy").click(function (e) {
            e.preventDefault();
            var id = jQuery(this).data("id");
            var sel = "#fancybox"+id+" .gallery:nth-child(1)";

            jQuery(sel).trigger("click");
            jQuery($).fancybox({
                selector : "[data-fancybox]",
                loop     : true
            });
        });

    }, 1500);
</script>