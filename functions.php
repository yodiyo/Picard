<?php
/**
 * Register Open Sans Google fonts for Picard.
 *
 * @return string
 */
function picard_open_sans_font_url() {
	$open_sans_font_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'picard' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'picard' );

		if ( 'cyrillic' == $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		} elseif ( 'greek' == $subset ) {
			$subsets .= ',greek,greek-ext';
		} elseif ( 'vietnamese' == $subset ) {
			$subsets .= ',vietnamese';
		}

		$query_args = array(
			'family' => urlencode( 'Open Sans:300italic,400italic,600italic,700italic,300,400,600,700' ),
			'subset' => urlencode( $subsets ),
		);

		$open_sans_font_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	return $open_sans_font_url;
}

/**
 * Register Montserrat Google fonts for Picard.
 *
 * @return string
 */
function picard_montserrat_font_url() {
	$montserrat_font_url = '';

	/* translators: If there are characters in your language that are not supported
	   by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'picard' ) ) {

		$montserrat_font_url = add_query_arg( 'family', urlencode( 'Montserrat:400,700' ), "//fonts.googleapis.com/css" );
	}

	return $montserrat_font_url;
}

function picard_scripts() {
	wp_enqueue_style( 'picard-style', get_stylesheet_uri(), '20141230' );

	wp_register_script( 'picard-script', get_template_directory_uri() . '/picard.js', array( 'jquery' ), '20150204', true );

	wp_enqueue_script( 'picard-script' );
}
add_action( 'wp_enqueue_scripts', 'picard_scripts' );

function get_json( $_post ) {
	foreach ( $_post as $post ) {
		$_post['post_class'] = implode( ' ', get_post_class( $_post['ID'] ) );
	}
	return $_post;
}

add_filter( 'json_prepare_post', 'get_json' );

function picard_api_init() {
	global $picard_api_comments;

	$picard_api_comments = new Picard_API_Comments();
	add_filter( 'json_endpoints', array( $picard_api_comments, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'picard_api_init' );

class Picard_API_Comments {
	public function register_routes( $routes ) {
		$routes['/picard/comments'] = array(
			array( array( $this, 'new_post' ), WP_JSON_Server::CREATABLE ),
		);

		return $routes;
	}

	public function new_post() {
		$commentdata = array(
			'comment_post_ID'      => $_POST['comment_post_ID'],
			'comment_author'       => $_POST['comment_author'],
			'comment_author_email' => $_POST['comment_author_email'],
			'comment_author_url'   => $_POST['comment_author_url'],
			'comment_content'      => $_POST['content'],
			'comment_author_IP'    => $_SERVER['REMOTE_ADDR'],
		);
		$comment_id = wp_new_comment( $commentdata );
		error_log( $comment_id );
	}
}
