<?php
 


		/*
        @ register woo_sync_update_before_publish as ajax function
		@ Download products/categories/tags/attributes/tax classes/shipping classes/product variations data and save them in database if data is not already exist
		@ This function is called by an ajax request from setting.js when user click on "update latest changes"
		@ All datas reads from "Woo sync target"
		*/
		
          add_action('wp_ajax_nopriv_woo_sync_update_before_publish', 'woo_sync_update_before_publish');
          add_action('wp_ajax_woo_sync_update_before_publish', 'woo_sync_update_before_publish');
          
          function woo_sync_update_before_publish()
              {
				  die('This Feature is only available on premium version . <a class="woo-sync-buyPremium" href="https://www.codester.com/items/11458/woocommerce-store-management-by-localhost?ref=sjafarhosseini007" target="_blank">Buy Premium Version Now</a>');
			  }
 
 		/*
        @ register woo_sync_delete_change_log as ajax function
		@ Clear woo sync target plugin latest changes log
		@ This function is called by an ajax request from setting.js when user click on "Clear latest changes log"
		*/         
          
          add_action('wp_ajax_nopriv_woo_sync_delete_change_log', 'woo_sync_delete_change_log');
          add_action('wp_ajax_woo_sync_delete_change_log', 'woo_sync_delete_change_log');
          
          function woo_sync_delete_change_log()
              {  
				  die('This Feature is only available on premium version');
              }
  
?>