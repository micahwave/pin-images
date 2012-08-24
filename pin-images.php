<?php

/**
 * Plugin Name: Pin Images
 * Description: Adds a Pin It button to images within your post content.
 * Author: Micah Ernst
 */

if( !class_exists( 'Pin_Images' ) ) {

class Pin_Images {
	
	/**
	 * Register some hooks and filters
	 */
	function __construct() {

		add_action( 'wp_head', array( $this, 'styles' ) );

		if( !is_admin() ) {
			add_filter( 'the_content', array( $this, 'add_image_markup' ) );
		}
	}

	/**
	 * Add some styles for the markup were adding
	 *
	 * @todo Move this to a stylesheet
	 */
	public function styles() {
		?>
		<style type="text/css">
		.pin-image-wrap {
			position: relative;
		}
		.pin-image-wrap button {
			position: absolute;
			bottom: 30px;
			right: 20px;
			width: 60px;
			height: 60px;
			background: url(<?php echo plugins_url( 'pin-images/images/big-p-button.png', dirname(__FILE__) ); ?>) no-repeat;
			z-index: 9999;
			cursor: pointer;
			display: none;
		}
		.pin-image-wrap:hover button {
			display: block;
		}
		</style>
		<?php
	}

	/**
	 * Look through the content of the post and wrap any images with some extra markup
	 */
	public function add_image_markup( $content ) {

		$dom = new DOMDocument;
		@$dom->loadHTML( $content );

		$imgs = $dom->getElementsByTagName('img');

		if( count( $imgs ) ) {

			foreach( $imgs as $img ) {

				// create div, append img
				$div = $dom->createElement( 'div' );
				$div->setAttribute( 'class', 'pin-image-wrap' );
				$div->appendChild( $img->cloneNode() );

				// add our button
				$btn = $dom->createElement( 'button' );

				// setup the url for the button
				$pinterest_url = add_query_arg( array(
					'url' => get_permalink(),
					'media' => $img->getAttribute( 'src' ),
					'description' => get_the_title()
				), 'http://pinterest.com/pin/create/button/' );

				// setup the onclick
				$btn->setAttribute( 'onclick', 'window.open("' . $pinterest_url . '", "Pinterest", "scrollbars=no,menubar=no,width=600,height=380,resizable=yes,toolbar=no,location=no,status=no");return false;' );

				$div->appendChild( $btn );
	
				$img->parentNode->replaceChild( $div, $img );
			}

			$content = $dom->saveHTML();

		}

		return $content;
	}

}

$GLOBALS['pin_images'] = new Pin_Images();

}