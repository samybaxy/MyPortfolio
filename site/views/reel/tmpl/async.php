<?php
/**
 * MyPortfolio Reel Ajax Async Layout
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
$jPath = JUri::base();

$input = JFactory::getApplication()->input;
$id = $input->get('id', '', 'INT');

//Load Jquery library
$doc = JFactory::getDocument();
	
//Dimensions for template
$lwidth = $this->params->get('lwidth');
if(!$lwidth) {
    $lwidth = '500';
}

$lwidth = str_replace('px', '', $lwidth);
if($lwidth < 500) {
    $lwidth = '500';
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

$css_code = <<<EOD
    .slidesjs-container {
        height: $lheight;          
        border: 1px solid #fff;
     }

    #slides .next {
        left: $lwidth;
    }

EOD;
	
$session = JFactory::getSession();
$totalPages = $session->get('totalPgs');
?>

	<div id="preSlidex" class="span12">
			<?php foreach ($this->data as $project) { ?>
			<div class="reelContainer row">
			<div class="slideContainer span7">
			<div id="slides" class="slides">
				<?php foreach ($project[1] as $img) { ?>
						<img src="images/myportfolio/<?php echo $project[2] 
														. "/" . $project[0]->alias
														. "/" . $img;?>" alt=""  style="height: <?php echo $lheight.'px'; ?>; width: <?php echo $lwidth.'px'; ?>;"
														width="<?php //echo $lwidth; ?>" height="<?php //echo $lheight; ?>" />
				<?php } ?>
			<a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
      		<a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
			</div>
			</div>
			<div class="reelContent span5">
				<div class="nameDesc">
					<h1><?php echo ucfirst($project[0]->project); ?></h1>
					<span class="sDesc"><?php echo $project[0]->short_description; ?></span>
				</div>
				<div class="Desc">
					<?php echo $project[0]->description; ?>
				</div>
				<div class="clientLaunch">
					<a href="<?php echo $project[0]->url; ?>" target="_blank" title="launch" class="button">
						<span>Launch</span>
					</a>
				</div>
			</div>
			</div>
			<?php  } ?>
			
			<div class="pagination" class="row">
				<ul class="paginate pag clearfix">
<?php
				if($this->page > 1) {
					$prev = ($this->page - 1);
					$ajxLink = JRoute::_('index.php?page=' . $prev . '&id='.$id.'&task=featProj&view=reel&format=raw');
					echo '<li><a href="'.$ajxLink.'" class="featProj">prev</a></li>';
				 }
				
				for($i = 1; $i <= $totalPages; $i++) {
					// for each page number
	                if($this->page == $i) {
	                	// if this page were about to echo = the current page
	                	echo '<li class="current">'. $i .'</li>';
		            }
		            else {
						$ajxLink = JRoute::_('index.php?page='. $i .'&id='.$id.'&task=featProj&view=reel&format=raw');
		                echo '<li><a href="'.$ajxLink.'" class="featProj">'. $i .'</a></li> ';
		            }
	            }
	
				if($this->page < $totalPages) {
					// is there a next page?
	                $next = ($this->page + 1); // if so, add 1 to the current
	                $ajxLink = JRoute::_('index.php?page=' . $next . '&id='.$id.'&task=featProj&view=reel&format=raw');
	                echo '<li><a href="'.$ajxLink.'" class="featProj">next</a></li>';
	            }
	?> 			</ul>
				</div>
        <script>
            jQuery.noConflict();
            jQuery(document).ready(function() {
                jQuery('#preloader').hide();
                jQuery('.featProj').click(function(e) {
                    e.preventDefault();
                    jQuery('#preloader').fadeIn( 1000 );
                    jQuery('#myContentLoad').fadeOut( 500 );

                    var url = jQuery(this).attr('href');
                    jQuery.post(url).success( function(data) {
                        setTimeout(function () {
                            jQuery('#preloader').fadeOut( 1000 );
                            jQuery('#myContentLoad').html( data );
                            jQuery('#myContentLoad').fadeIn( 1000 );

                            jQuery(".slides").slidesjs({
                                width: <?php echo $lwidth; ?>,
                                height: <?php echo $lheight; ?>,
                                navigation: {
                                    active: false,
                                    effect: "fade"
                                },
                                effect: {
                                    fade: {
                                        speed: 400
                                    },
                                },
                                pagination: {
                                    active: true,
                                    // [boolean] Create pagination items.
                                    // You cannot use your own pagination. Sorry.
                                    effect: "fade"
                                    // [string] Can be either "slide" or "fade".
                                }
                            });
                        }, 1500);
                    })
                        .error(function(data) {
                            alert("No project defined under portfolio category");
                        });
                    return false;
                });
            });
        </script>
	</div>