<?php
/**
* MyPortfolio Virgo Frontend view
*
* @package      MyPortfolio
* @subpackage   components
 *
 *@copyright    Copyright (C) 2010 - 2018 SamyBaxy Inc. All rights reserved.
* @author		samybaxy
* @link         http://www.samybaxy.net
* @license	    GNU/GPL
*/

	// No direct access
	defined( '_JEXEC' ) or die;
	
	// load jpath root	
	$jPath = JUri::root();
	
	class MyPortfolioViewVirgo extends JViewLegacy {
		
		protected $data;
		protected $pList;
		
		function display( $tpl = null ) {

			//get Component wide parameters
			$params = JComponentHelper::getParams('com_myportfolio');
			$this->params = $params;
						
			$this->data		= $this->get('Portfolio', 'Myportfolio');
			$this->pList	= $this->get('CatProjects', 'Myportfolio');
			
			$this->_prepareDocument();
			parent::display($tpl);
		}
		
		
		protected function _prepareDocument() {			
			$app	= JFactory::getApplication();
			$menus	= $app->getMenu();
			$title	= null;
			
			// Because the application sets a default page title,
			// we need to get it from the menu item itself
			$menu = $menus->getActive();
			if($menu) {
				$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
			}
			
			$title = $this->params->get('page_title', '');
			
			if (empty($title)) {
				$title = $app->getCfg('sitename');
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			$this->document->setTitle($title);
			
			if ($this->params->get('menu-meta_description'))
			{
				$this->document->setDescription($this->params->get('menu-meta_description'));
			}
			
			if ($this->params->get('menu-meta_keywords'))
			{
				$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
			}
			
			if ($this->params->get('robots'))
			{
				$this->document->setMetadata('robots', $this->params->get('robots'));
			}
			
		}

		// view called for the project task
		function ajaxLoadProject() {
			//get Component wide parameters
			$params = JComponentHelper::getParams('com_myportfolio');			
			$this->params = $params;

            //param for new ribbon image
            $ribbon = $this->params->get('ribbon');

			//getVar
			$input = JFactory::getApplication()->input;
			$cid = $input->get('id', '', 'GET');

			$jPath = JUri::root();			
			$doc = JFactory::getDocument();

			$data	= $this->get('Project', 'Myportfolio');
			
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
			if($lheight < 300) {
				$lheight = '300';
			}
			$lheight = str_replace('px', '', $lheight);
			
			$twidth = $this->params->get('twidth');
			if(!$twidth) {
				$twidth = '90px';
			}
			$theight = $this->params->get('theight');
			if(!$theight) {
				$theight = '90px';
			}
?>
<div id="newHope" class="span8">
    <div id="example">
        <?php if($ribbon == 1): ?>
        <img src="<?php echo $jPath; ?>components/com_myportfolio/views/virgo/assets/images/new-ribbon.png" width="112" height="112" alt="New Ribbon" id="ribbon">
        <?php endif; ?>
        <div id="slides">
            <?php foreach ($data[1] as $k => $img) { ?>
                <img src="<?php echo $jPath ?>images/myportfolio/<?php echo $data[2]. "/" . $data[0]->alias. "/" . $img;?>"  width="" height="" />
            <?php } ?>

            <a href="#" class="slidesjs-previous slidesjs-navigation"><i class="icon-chevron-left icon-large"></i></a>
            <a href="#" class="slidesjs-next slidesjs-navigation"><i class="icon-chevron-right icon-large"></i></a>
        </div>
        <img src="<?php echo $jPath;?>components/com_myportfolio/views/virgo/assets/images/example-frame.png" width="<?php echo $lwidth; ?>" height="<?php echo $lheight; ?>" alt="Example Frame" id="whiteframe">
    </div>
</div>
<div class="flitems span4">
    <h4><?php echo $data[0]->project; ?></h4>
    <p class="client"><span><?php echo JText::_('COM_MYPORTFOLIO_CLIENT'); ?></span> <?php echo $data[0]->client; ?></p>
    <div><?php echo $data[0]->description; ?></div>
    <a href="<?php echo $data[0]->url;?>" target="_blank" style="color: #08c!important; :hover: #005580;"><?php echo $data[0]->url;?></a>
</div>
<script>
    jQuery.noConflict();
    jQuery(function() {
        jQuery('#preloader').hide();

        jQuery('.projects').click(function(e) {
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
</script>
<?php
}
	}