<?php
/**
 * myPortfolio Administrator cpanel renderrer
 * 
 * @package		myPortfolio
 * @subpackage	component
 * @link		http://www.samybaxy.net
 * @license		GNU/GPLv3
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class myportfolioRenderCpanel {
	function quickIcon ( $link, $image, $text ) {		
		$lang	= &JFactory::getLanguage();
		$button = '';
		if ($lang->isRTL()) {
			$button .= '<div style="float:right;">';
		} else {
			$button .= '<div style="float:left;">';
		}
		$button .=	'<div class="icon">'
				   .'<a href="'.$link.'">'
				   .JHTML::_('image.site',  $image, '/components/com_myportfolio/assets/images/', NULL, NULL, $text )
				   .'<span>'.$text.'</span></a>'
				   .'</div>';
		$button .= '</div>';

		return $button;
	}
}