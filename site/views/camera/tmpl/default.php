<?php
/**
 * MyPortfolio Camera Site Layout
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
	$jPath = JUri::root();

	//Load Jquery library
	$doc = JFactory::getDocument();
	$doc->addScript ( 'http://code.jquery.com/jquery-latest.min.js' );
	$doc->addScript ($jPath.'media/com_myportfolio/js/jquery.js');

	//load CSS
	$doc->addStyleSheet ($jPath.'components/com_myportfolio/views/camera/assets/css/standard.css');
	$doc->addStyleSheet ($jPath.'components/com_myportfolio/views/camera/assets/css/camera.css');

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

		.camera_wrap {
			border: 1px solid #e7e7e7;
			padding: 5px;
		}

		.camera_caption {
			color: #fff;
		}

		.camera_wrap .camera_pag .camera_pag_ul {
			display:block;
		}

		.camera_thumbs_cont > div {
			display:block;
		}";

	$doc->addStyleDeclaration($css_code);

    $ajaxx = <<<EOD
     var jq = jQuery.noConflict();
       jq(document).ready(function() {
            jq('#preloader').hide();
    
            jq('.ftproj').click(function(e) {
                    e.preventDefault();			                
                    jq('#preloader').fadeIn( 1000 );			                
                    jq('#myContentLoadsld').fadeOut( 500 );
                                                
                    var url = jq(this).attr('href');		                
                    jq.post(url)
                        .success( function(data) {
                            //TODO myContent (div) show with loader
                            setTimeout(function () {
                                jq('#preloader').fadeOut( 500 );
                                jq('#myContentLoadsld').html( data );
                                jq('#myContentLoadsld').fadeIn( 1000 );					                    
                            }, 1500);
                        })
                        .error(function(data) {
                            alert("No project defined under portfolio category");
                        });
                    return false;
            });
        });
EOD;
$doc->addScriptDeclaration( $ajaxx );
?>
<div id="myPortfolio">
    <div id="preContentLoad">
        <div id="preloader" class="row">
            <div class="preImg">
                <img src="<?php echo 'components/com_myportfolio/views/camera/assets/images/ajax-loader.gif'; ?>" alt="loader image" />
            </div>
        </div>
        <div id="myContentLoad" class="row">
            <div id="myContentLoadsld" class="span12">
                <h4><?php echo ucfirst($this->data[0]->name); ?></h4>
                <div class="camera_wrap camera_azure_skin" id="camera_wrap">
                    <?php
                        $i = 1;
                        $v = $this->data[1];
                        $path   = JPATH_SITE.DS.'images'.DS.'myportfolio'.DS.$this->data[0]->alias.DS.$v->alias;
                        $JPath = 'images/myportfolio/'.$this->data[0]->alias.'/'.$v->alias;
                        if(JFolder::exists($path)) {
                            $img = JFolder::files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('index.html'));
                        }
                        else {
                            $img[0] = "noimage.jpg";
                        }
                        foreach ($img as $image) :
                    ?>
                        <div
                            data-src="<?php echo $JPath . "/" . $image; ?>"
                            data-thumb="<?php echo $JPath. "/" . $image; ?>"
                            data-target="_blank"
                            data-link="<?php echo $v->url; ?>">
                            <div class="camera_caption fadeFromBottom">
                                <?php echo $i <= 1 ? $v->short_description : ''; ?>
                                <div class="fadeIn camera_effected">
                                    <span><strong><?php echo JText::_('COM_MYPORTFOLIO_CLIENT'); ?></strong><span style='color: #fff;'> <?php echo ucfirst($v->client); ?></span></span>
                                    <span class="fltUrl" ><strong><?php echo JText::_('COM_MYPORTFOLIO_URL'); ?></strong><span style='color: #fff;'><a href="<?php echo $v->url; ?>"> <?php echo $v->url; ?></a></span></span>
                                </div>
                            </div>
                        </div>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
                <div>
                    <?php echo $v->description; ?>
                </div>
            </div>
            <div id="portListNoS" class="row">
<h3><?php echo JText::_('COM_MYPORTFOLIO_PROJECT'); ?></h3>
                <ul class="windowList span12">

<?php
    foreach($this->pList[1] as $pList) {
        $link = JRoute::_( 'index.php?task=project&view=camera&pid='.$pList->id .'&format=raw' );
        $catAlias = str_replace(' ', '-', $this->data[0]->alias);
?>					<li>
                    <ul class="secList">
                        <li>
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
                            <a href="<?php echo $link; ?>" class='ftproj myportfoliofade' target="_blank" title="<?php echo $pList->alias; ?>"  style="display: block; padding: 3px; border: 1px solid #ccc!important; overflow: hidden; width: <?php echo $twidth; ?>; height: <?php echo $theight; ?>;">
                                <img src="<?php echo $JPath.'/'.$img[0]; ?>"
                                 alt="" width="<?php //echo $twidth; ?>" height="<?php //echo $theight; ?>"  style="max-width: 100%; height: <?php echo $theight; ?>;" />
                            </a>
                            <a href="<?php echo $link; ?>" class='ftproj spcLinks'><?php echo ucfirst($pList->project); ?></a>
                        </li>
                    </ul>
                </li>
                    <?php } ?>
                    </ul>
            </div>
        </div>
    </div>
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