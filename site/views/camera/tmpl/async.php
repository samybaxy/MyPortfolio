<?php
/**
 * MyPortfolio Camera Ajax Async Site Layout
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

	//Load Jquery library
	$doc = JFactory::getDocument();
		
	//Dimensions for template
	$lwidth = $this->params->get('lwidth');
	if(!$lwidth) {
		//$lwidth = '550px';
	}
	$lheight = $this->params->get('lheight');
	if(!$lheight) {
		//$lheight = '250px';
	}
	$twidth = $this->params->get('twidth');
	if(!$twidth) {
		$twidth = '90px';
	}
	$theight = $this->params->get('theight');
	if(!$theight) {
		$theight = '90px';
	}
		
	// embedded style for easyslider plugin
	$css_code = "
			.camera_caption > div {
				background: rgba(0,0,0, 0.7);
				font-family: 'Times New Roman', serif ;
				font-size: 15px;
				display:block;
			}
			.camera_caption {
				color: #FFFFFF;
			}
			.camera_wrap .camera_pag .camera_pag_ul {
				display:block;
			}
			.camera_thumbs_cont > div {
				display:block;
			}";
		
	$doc->addStyleDeclaration($css_code);
		
	$ajaxxxxes = <<<EOD
			 var jqr = jQuery.noConflict();
			   jqr(function($) {
			   		jqr('#preloader').hide();
	
			        jqr('.projects').click(function(e) {
			                e.preventDefault();
			                jqr('#preloader').fadeIn( 1000 );
					        jqr('#myContentLoad').fadeOut( 500 );
					  
			                var url = jqr(this).attr('href');
			                jqr.post(url)
				                .success( function(data) {
				                    //TODO myContent (div) show with loader
				                     setTimeout(function () {
				                    	jqr('#preloader').fadeOut( 500 );
					                    jqr('#myContentLoad').html( data );
					                    jqr('#myContentLoad').fadeIn( 500 );
				                    }, 1500);
				                })
				                .error(function(data) {
				                	alert("No project defined under portfolio category");
				                });
			                return false;
			        });
			    });
EOD;

$doc->addScriptDeclaration( $ajaxxxxes );
?>
<h4><?php echo ucfirst($this->data[0]->project); ?></h4>
<div class="camera_wrap camera_azure_skin" id="camera_wrap">
    <?php $i = 1; ?>
    <?php foreach ($this->data[1] as $k => $img) { ?>
        <div
            data-src="<?php echo $jPath ?>images/myportfolio/<?php echo $this->data[2]. "/" . $this->data[0]->alias. "/" . $img;?>"
            data-thumb="<?php echo $jPath ?>images/myportfolio/<?php echo $this->data[2]. "/" . $this->data[0]->alias. "/" . $img;?>"
            data-target="_blank"
            data-link="<?php echo $this->data[0]->url; ?>">
        <div class="camera_caption fadeFromBottom">
            <?php echo $i <= 1 ? $this->data[0]->short_description : ''; ?>
            <div class="fadeIn camera_effected">
                <span><strong><?php echo JText::_('COM_MYPORTFOLIO_CLIENT'); ?></strong><span style='color: #fff;'> <?php echo ucfirst($this->data[0]->client); ?></span></span>
                <span class="fltUrl" ><strong><?php echo JText::_('COM_MYPORTFOLIO_URL'); ?></strong><span style='color: #fff;'><a href="<?php echo $this->data[0]->url; ?>"> <?php echo $this->data[0]->url; ?></a></span></span>
            </div>
        </div>
        </div>
    <?php $i++; ?>
    <?php } ?>
</div>
<div>
    <?php echo $this->data[0]->description; ?>
</div>
<script src="<?php echo $jPath; ?>components/com_myportfolio/views/camera/assets/js/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $jPath; ?>components/com_myportfolio/views/camera/assets/js/jquery.mobile.customized.min.js" type="text/javascript"></script>
<script src="<?php echo $jPath; ?>components/com_myportfolio/views/camera/assets/js/jquery.easing.1.3.js" type="text/javascript"></script>
<script src="<?php echo $jPath; ?>components/com_myportfolio/views/camera/assets/js/camera.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var $jqu = jQuery.noConflict();
    $jqu(document).ready(function() {
        $jqu("#camera_wrap").camera({
            loader: "pie",
            easing: "easeInOutElastic",
            loaderStroke: 10,
            loaderPadding: 3,
            height: "40%",
            loaderOpacity:0.7,
            loaderColor:"#717274",
            loaderBgColor:"#222222",
            pieDiameter:50,
            piePosition:"rightTop",
            barPosition:"bottom",
            barDirection:"leftToRight",
            fx:"random",
            alignment:"center",
            autoAdvance:true,
            hover:true,
            navigationHover:true,
            overlayer:true,
            pagination:true,
            portrait:false,
            cols:6,
            rows:4,
            slicedCols:12,
            slicedRows:8,
            thumbnails:true,
            time:10000,
            transPeriod:1500,
            mobileAutoAdvance:true,
            mobileNavHover:true,
        });
    });
</script>