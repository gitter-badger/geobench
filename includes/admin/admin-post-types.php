<?php
/**
 * Admin for GeoBench Post Types
 *
 * What happens in WordPress admin when handling GeoBench post types and taxonomies.
 *
 * @package GeoBench/Admin
 */

namespace GeoBench\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Types Admin for GeoBench.
 */
class Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param  string $text
	 * @param  object $post
	 *
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'map' :
				$text = __( 'Map name', 'geobench' );
				break;
		}
		return $text;
	}

}

new Post_Types();
