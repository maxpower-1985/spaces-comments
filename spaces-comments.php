<?php
/**
 * Plugin Name: Spaces – Comments
 * Plugin URI: spaces.th-koeln.de
 * Description: Create a new comment with reply-function.
 * The Plugin creates a new way for posting comments by adding
 * a reply function and some overall improvements.
 * Works best with the spaces editor.
 * Author: Spaces TH Köln
 * Author URI: spaces.th-koeln.de
 * Version: 1.0
 * Text Domain: spaces-comments-domain
 * Domain Path: /languages
 * @package WordPress
 * @subpackage defaultspace
 * TODO:
 */
?>
<?php
function spaces_comments_activate(){
	if ( ! function_exists( 'is_plugin_active_for_network()')){
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}
}
register_activation_hook(__FILE__, 'spaces_comments_activate');
?>
<?php
add_action( 'wp_enqueue_scripts', 'vue_comments_scripts_styles' );
/**
 * Undocumented function
 *
 * @return void
 */
function vue_comments_scripts_styles() {
	$theme = wp_get_theme();
	$plugin_path = plugin_dir_url( __FILE__ );
	if ('defaultspace' == $theme->name || 'defaultspace' == $theme->parent_theme){
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		if ( class_exists( 'Spaces_Editor_Comments' ) === true ) {
			$comment_editor = 'true';
		} else {
			$comment_editor = 'false';
		}
			wp_enqueue_script( 'vuejs', 'https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.11/vue.min.js', array(), '2.6.11', false );
			$file = 'build/public/js/post-comment-section.min.js';
			$time = filemtime( dirname( __DIR__ ) . '' . $file );
			wp_enqueue_script( 'vue-comments', $plugin_path . $file, array(), $time, false );
			wp_localize_script( 'vue-comments', 'commentEditor', $comment_editor );
	}
}
}

/**
 * Converts php DateTime format to Javascript Moment format.
 *
 * @param string $php_format Get the WordPress time- or dateformat.
 *
 * @return string
 */
function convert_php_to_js_moment_format( string $php_format ): string {
	$replacements = array(
		'A' => 'A',
		'a' => 'a',
		'B' => '',
		'c' => 'YYYY-MM-DD[T]HH:mm:ssZ',
		'D' => 'ddd',
		'd' => 'DD',
		'e' => 'zz',
		'F' => 'MMMM',
		'G' => 'H',
		'g' => 'h',
		'H' => 'HH',
		'h' => 'hh',
		'I' => '',
		'i' => 'mm',
		'j' => 'D',
		'L' => '',
		'l' => 'dddd',
		'M' => 'MMM',
		'm' => 'MM',
		'N' => 'E',
		'n' => 'M',
		'O' => 'ZZ',
		'o' => 'YYYY',
		'P' => 'Z',
		'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ',
		'S' => 'o',
		's' => 'ss',
		'T' => 'z',
		't' => '',
		'U' => 'X',
		'u' => 'SSSSSS',
		'v' => 'SSS',
		'W' => 'W',
		'w' => 'e',
		'Y' => 'YYYY',
		'y' => 'YY',
		'Z' => '',
		'z' => 'DDD',
	);

	// Converts escaped characters.
	foreach ( $replacements as $from => $to ) {
		$replacements[ '\\' . $from ] = '[' . $from . ']';
	}

	return strtr( $php_format, $replacements );
}
?>
<?php
/**
 * Get all the needed information for the commentUser window object
 *
 * @return object
 */
function get_current_user_infos() {
	$current_user = wp_get_current_user();
	$lang = get_locale();
	$php_date_format = get_option( 'date_format' );
	$php_time_format = get_option( 'time_format' );
	$js_date_format  = convert_php_to_js_moment_format( $php_date_format );
	$js_time_format  = convert_php_to_js_moment_format( $php_time_format );

	/*todo Check if user can read other profiles*/
	if ( is_object( spaces() ) ) {
		if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			$blog_profile = spaces()->blogs_profile->get_profile_url() . '=';
		}
	} else {
		$blog_profile = '';
	}

	$current_user_info = array(
		'allcaps'     => $current_user->allcaps,
		'email'       => $current_user->user_email,
		'id'          => $current_user->id,
		'lang'        => $lang,
		'timeFormat'  => $js_time_format,
		'dateFormat'  => $js_date_format,
		'blogProfile' => $blog_profile,
	);
	return $current_user_info;
}
?>
<?php
add_action( 'wp_enqueue_scripts', 'register_scripts' );
/**
 * Register all dependencies
 *
 * @return void
 */
function register_scripts() {
	wp_register_script( 'vue-comments_user', '', 'vue-comments', 1.0, false );
	wp_register_script( 'vue_comments_post', '', 'vue-comments', 1.0, false );
}
?>
<?php
add_action( 'wp_enqueue_scripts', 'enqueue_vue_comments_user' );
/**
 * Create window variables commentUser and commentTranslate for the frontend
 *
 * @return void
 */
function enqueue_vue_comments_user() {
	$comments_translate = array(
		'askDeleteComment' => esc_html__( 'Do you really want to delete this comment?', 'defaultspace' ),
		'editComment'      => esc_html__( 'Edit comment', 'defaultspace' ),
		'replyComment'     => esc_html__( 'Reply to comment', 'defaultspace' ),
		'deleteComment'    => esc_html__( 'Delete comment', 'defaultspace' ),
		'yourComment'      => esc_html__( 'Your comment', 'defaultspace' ),
		'at'               => esc_html__( 'at', 'defaultspace' ),
		'reply'            => esc_html__( 'Reply', 'defaultspace' ),
		'profile'          => esc_html__( 'profile', 'defaultspace' ),
		'goToProfileOf'    => esc_html__( 'Go to profile of', 'defaultspace' ),
		'showAllpostsOf'   => esc_html__( 'Show posts in this Space', 'defaultspace' ),
		'addNewComment'    => esc_html__( 'Add a new comment', 'defaultspace' ),
		'deletedComment'   => esc_html__( 'Comment has been deleted', 'defaultspace' ),
	);
	wp_localize_script( 'vue-comments_user', 'commentTranslate', $comments_translate );
	wp_enqueue_script( 'vue-comments_user' );
	global $post;
	if ( is_user_logged_in() && is_single( $post ) ) {
		$current_user_info = get_current_user_infos();
		$comment_user      = json_encode(
			array(
				'current'    => $current_user_info,
				'avatar_url' => get_avatar_url( get_current_user_id() ),
				'superadmin' => is_super_admin( $current_user_info->id ),
			)
		);
		$comment_user      = "var commentUser = ${comment_user};";
		wp_add_inline_script( 'vue-comments_user', $comment_user, 'after' );
	}
}
?>
<?php
add_action( 'wp_enqueue_scripts', 'enqueue_vue_comments_post' );
/**
 * Create the window variable commentPost for the frontend
 *
 * @return void
 */
function enqueue_vue_comments_post() {
	wp_enqueue_script( 'vue_comments_post' );
	global $post;
	if ( is_user_logged_in() && is_single( $post ) ) {
		$comment_post = json_encode(
			array(
				'current_post_id'     => $post->ID,
				'current_post_author' => $post->post_author,
			)
		);
		$comment_post = "var commentPost = ${comment_post};";
		wp_add_inline_script( 'vue-comments_user', $comment_post, 'after' );
	}
}
add_action( 'rest_api_init', 'add_custom_field_display_name_to_comment' );
/**
 * Add custom field display_name to comment
 *
 * @return void
 */
function add_custom_field_display_name_to_comment() {
	register_rest_field(
		'comment',
		'display_name',
		array(
			'get_callback'    => 'get_custom_display_name',
			'update_callback' => null,
			'schema'          => null,
		)
	);
}

/**
 * Get the user display_name
 *
 * @param array $object The comment object .
 * @param mixed $fieldname The fieldname.
 * @param mixed $request The request.
 * @return string $display_name|$user_login The user display_name or user login if display_name empty.
 */
function get_custom_display_name( $object, $fieldname, $request ) {
	$user = get_user_by( 'id', $object['author'] );

	$display_name = $user->{'data'}->{'display_name'};
	$user_login   = $user->{'data'}->{'user_login'};
	if ( '' === $display_name ) {
		return $user_login;
	}
	return $display_name;
}
