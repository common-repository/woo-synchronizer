<?php
/**
 * Plugin Name: Woocommerce Synchronizer
 * Description: Synchronize products between localhost and online store
 * Author: S.J.Hossseini
 * Author URI: https://t.me/ttmga
 * Version: 0.1
 * Plugin URI: https://woo.ttmga.com/woo-sync-en/
 * Text Domain: woocommerce-synchronizer
 * Domain Path: /languages
 */

 
// Set php.ini values for localhost 
ini_set ( 'max_execution_time' , 259200 );
ini_set ( 'memory_limit' ,  -1 );
ini_set ( 'max_input_time' ,  -1 ); 
ini_set ( 'max_input_vars' ,  99999999 ); 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WOO_SYNC_PLUGIN_FILE as file path.
if ( ! defined( 'WOO_SYNC_PLUGIN_FILE' ) ) {
	define( 'WOO_SYNC_PLUGIN_FILE', plugin_dir_path( __FILE__) );
	define( 'WOO_SYNC_FILE_URL', plugins_url('', __FILE__).'/' );
}


if(!function_exists('wp_get_current_user')) {
include(ABSPATH . "wp-includes/pluggable.php"); 
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
}	

///load main files
require WOO_SYNC_PLUGIN_FILE .'includes/class-tools.php';
require WOO_SYNC_PLUGIN_FILE .'includes/functions.php';
require WOO_SYNC_PLUGIN_FILE .'includes/class-rest-api.php';
require WOO_SYNC_PLUGIN_FILE .'includes/actions.php';
require WOO_SYNC_PLUGIN_FILE .'includes/publisher.php';
//include parts of syncronizer
include WOO_SYNC_PLUGIN_FILE .'admin/setting.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/target/tags.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/target/products.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/target/categories.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/target/attributes.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/local/create.php';
include WOO_SYNC_PLUGIN_FILE .'includes/synchronizer/local/update.php';

	

/////////////////////////////////////////////////////////////// load admin scriptS
   function woo_sync_add_admin_scripts( $hook ) {
	   wp_register_style('woo_sync_dashicons', plugins_url('/assets/css/woo-sync.css', __FILE__));
       wp_enqueue_style('woo_sync_dashicons');

   
	   
	  if (isset($_GET['page']) && ($_GET['page'] == 'woo_sync_dash')){  
        wp_enqueue_script( 'jquery' );
		wp_enqueue_script('jquery-ui-tabs');
	    wp_enqueue_style(  'woo_sync_setting_page', plugins_url('/assets/css/setting.css', __FILE__));
	    
		wp_register_script('woo_sync_dashboard_js', plugins_url('/assets/js/setting.js', __FILE__),array('jquery'),'',true);
        $translation_array = array(
         'storeurlerr' => __( 'Target store url field should not be empty!', 'woocommerce-synchronizer' ),
         'ckerror' => __( 'Consumer key field should not be empty!', 'woocommerce-synchronizer' ),
         'skerror' => __( 'Consumer secret field should not be empty!', 'woocommerce-synchronizer' ),	
         'ftpporterr' => __( 'FTP port field should not be empty!', 'woocommerce-synchronizer' ),
         'ftpusererror' => __( 'FTP username field should not be empty!', 'woocommerce-synchronizer' ),	 
         'ftppasserror' => __( 'FTP password field should not be empty!', 'woocommerce-synchronizer' ),
         'preaper' => __( 'preparing...', 'woocommerce-synchronizer' ),	
         'attribute' => __( 'Getting attributes of Products...', 'woocommerce-synchronizer' ),	
         'tags' => __( 'Getting tags of Products...', 'woocommerce-synchronizer' ),	
         'category' => __( 'Getting categories of Products...', 'woocommerce-synchronizer' ),	
         'product' => __( 'Getting all Products...', 'woocommerce-synchronizer' ),	
         'tax' => __( 'Getting all tax classes...', 'woocommerce-synchronizer' ),	
         'shipping' => __( 'Getting all shipping classes...', 'woocommerce-synchronizer' ),			 
         'product_variation' =>	__( 'Getting variations of Products...', 'woocommerce-synchronizer' ),
         'Confirmreq' =>	__( 'Are you sure you want to clear the download history?', 'woocommerce-synchronizer' ),
         'ConfirmreqTime' =>	__( 'Are you sure you want to clear the last changes log?', 'woocommerce-synchronizer' ),		 
         'success' =>	__( 'Mission Accomplished.', 'woocommerce-synchronizer' ),
         'connectionlost' =>	__( 'Internet connection is not established...', 'woocommerce-synchronizer' )		 
        );
        wp_localize_script( 'woo_sync_dashboard_js', 'woo_sync_trans', $translation_array );	   
        wp_enqueue_script('woo_sync_dashboard_js');		  

	  }
    }
    add_action('admin_enqueue_scripts','woo_sync_add_admin_scripts',10,1);
	
/////////////////////////////////////////////////////////////////////plugin row elements

add_filter( 'plugin_row_meta', 'woo_sync_plugin_row_meta', 10, 2 );
 
function woo_sync_plugin_row_meta( $links, $file ) {    
    if ( plugin_basename( __FILE__ ) == $file ) {
        $row_meta = array(
          'Docs'    => '<a href="' . esc_url( 'https://woo.ttmga.com/woo-sync-en/Help.pdf' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Help File', 'woocommerce-synchronizer' ) . '" style="color:green;">' . esc_html__( 'Docs', 'woocommerce-synchronizer' ) . '</a>',
          'Upgrade'    => '<a href="' . esc_url( 'https://www.codester.com/items/11458/woocommerce-store-management-by-localhost?ref=sjafarhosseini007' ) . '" target="_blank" aria-label="' . esc_attr__( 'Premium', 'woocommerce-synchronizer' ) . '" style="color:blue;">' . esc_html__( 'Upgrade To Premium', 'woocommerce-synchronizer' ) . '</a>',		  
        );
 
        return array_merge( $links, $row_meta );
    }
    return (array) $links;
}	

?>