<?php
/*
Plugin Name: IndiaSan User or Author Image
Plugin URI: http://www.indiasan.com
Description: IndiaSan User or Author Image Plugin allows your website's users to set image to their profile. This will add more fun to your website!
Version: 1.0.3
Author: IndiaSan
Author URI: http://www.indiasan.com
Text Domain: indiasan-user-or-author-image
*/

/**
 * Basic plugin definitions 
 * 
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
if( !defined( 'ISAI_DIR' ) ) {
  define( 'ISAI_DIR', dirname( __FILE__ ) );      // Plugin dir
}
if( !defined( 'ISAI_URL' ) ) {
  define( 'ISAI_URL', plugin_dir_url( __FILE__ ) );   // Plugin url
}
if(!defined('ISAI_PREFIX')) {
  define('ISAI_PREFIX', 'isai_'); // Plugin Prefix
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
load_plugin_textdomain( 'indiasan-user-or-author-image', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Enqueue Scripts on Admin Side
 * 
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
function isai_admin_scripts( $hook ){
	
	if ( $hook != 'profile.php' ) {
        return;
    }
	
	global $wp_version;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'thickbox' );
	
	// For media uploader
	wp_enqueue_media();
	wp_enqueue_script( 'media-upload' );
	
	// Check wp version for showing media uploader
	$newui = $wp_version >= '3.5' ? '1' : '0';
	
	// Register and enqueue media uploader script
	wp_register_script( 'isai-media-uploader-script', ISAI_URL.'js/isai-media-uploader.js', array(), null, true );
	wp_enqueue_script( 'isai-media-uploader-script' );
	
	wp_localize_script( 'isai-media-uploader-script', 'isai', array( 
																		'new_media_ui' => $newui
																	));
}
add_action( 'admin_enqueue_scripts', 'isai_admin_scripts' );

/**
 * Add uploader to edit user profile
 *
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
function isai_upload_image( $description ){
	
	// Get the prefix
	$prefix = ISAI_PREFIX;
	
	$user_id      = get_current_user_id();
	$author_image = get_user_meta( $user_id, $prefix.'author_image', true );
	
	if ( IS_PROFILE_PAGE ) {
		
		ob_start();
?>
		<br><br>---- OR ----<br><br>
		<input type="text" name="<?php echo $prefix.'author_image'; ?>" id="<?php echo $prefix.'author_image'; ?>" class="regular-text" value="<?php echo $author_image; ?>" />
		<input type="button" name="<?php echo $prefix.'author_image_uploader'; ?>" id="<?php echo $prefix.'author_image_uploader'; ?>" class="button-secondary" value="<?php echo __( 'Choose Image', 'indiasan-user-or-author-image' ) ?>"><br />
		<span class="description"><?php echo __( 'Choose User or Author Image.', 'indiasan-user-or-author-image' ) ?></span>
<?php
		$html        = ob_get_clean();
		$description = $description.$html;
		
		return $description;
	}
}
add_filter( 'user_profile_picture_description', 'isai_upload_image', 2 );

/**
 * Save image on update user profile
 *
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
function isai_profile_update( $user_id, $old_user_data ){
	
	// Get the prefix
	$prefix = ISAI_PREFIX;
	
	$author_image = isset( $_POST[$prefix.'author_image'] ) ? $_POST[$prefix.'author_image'] : '';
	
	update_user_meta( $user_id, $prefix.'author_image', $author_image );
}
add_action( 'profile_update', 'isai_profile_update', 10, 2 );

/**
 * Display user's avatar if uploaded
 *
 * @package IndiaSan User or Author Image
 * @since 1.0.0
 */
function isai_get_avatar( $avatar, $id_or_email, $size, $default, $alt ){
	
	// Get the prefix
	$prefix = ISAI_PREFIX;
	
	$user_id = '';
	
	if ( is_numeric( $id_or_email ) ) {

        $user_id = (int) $id_or_email;

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $user_id = (int) $id_or_email->user_id;
        }

    }
	
    if( !empty( $user_id ) ){
    	
    	$author_image = get_user_meta( $user_id, $prefix.'author_image', true );
	
		if( !empty( $author_image ) ){
			
			$avatar = "<img alt='{$alt}' src='{$author_image}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		}	
    }
    
	return $avatar;
}
add_filter( 'get_avatar', 'isai_get_avatar', 1, 5 );
?>