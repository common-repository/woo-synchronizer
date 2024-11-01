<?php
     /*
     @create category
     @update category
     @delete category
     */
	 
	 
 
     add_action('created_product_cat', 'woo_sync_create_category', 10, 2);
     function woo_sync_create_category($term_id, $taxonomy_term_id)
     {
		 if (!get_option('woo_sync_creating_started')){
         $category      = get_term($term_id, 'product_cat');
		 $termmeta      = get_term_meta($category->term_id);
         $has_parent_id = woo_sync_gettargetsiteobjid($category->parent , 'category');
         if ($has_parent_id) {
             $parent = $has_parent_id;
         } else {
             $parent = $category->parent;
         }
		 
		 $has_a_id = woo_sync_gettargetsiteobjid($termmeta['thumbnail_id'][0] , 'media');

		 if (get_the_guid($termmeta['thumbnail_id'][0])){
			 
		if ($has_a_id){
		 $images =  array(
				              'id'=> $has_a_id
	               );
		}else{

        if (!get_option("woo_sync_is_local_host")){
		 $images =  array(
				              'src'=> get_the_guid($termmeta['thumbnail_id'][0])
	               );
		}else{			
		 $images =  array(
				              'src'=> get_option('woo_sync_url_cdn') . basename(get_the_guid($termmeta['thumbnail_id'][0]))
	               );
		}
        }
		
		 }else{
		 $images = null;	 
		 }
		 
		 if ($termmeta['display_type'][0]){
			 $display = $termmeta['display_type'][0];
		 }else{
			 $display = 'default';
		 }
		 
		 $parametrs = array(
                 'name' => $category->name,
                 'slug' => $category->slug,
                 'parent' => $parent,
                 'description' => $category->description,
                 'display' => $display,
				 'image' => $images
             );
				
         if (get_option('woo_sync_cat_immediately') == 'on'){
		if ($has_a_id == ''){
			 $is_local_host = get_option("woo_sync_is_local_host");
			 if ($is_local_host){			
		     $res = upload_category_image_before_publish($termmeta['thumbnail_id'][0]);
			 }
		}		 	 
         woo_sync_rest_api_category_handle( $parametrs , $term_id , 'create');
		 }else{
		  woo_sync_future_publish_category($parametrs , $term_id , 'create');	 
		 }
		 
		 }
     }
	 


     add_action('edited_product_cat', 'woo_sync_edit_category');
     function woo_sync_edit_category($term_id)
     {
		 		if (!get_option('woo_sync_creating_started')){
         $category      = get_term($term_id, 'product_cat');
         $termmeta      = get_term_meta($category->term_id);
         $has_parent_id = woo_sync_gettargetsiteobjid($category->parent , 'category');
         if ($has_parent_id) {
             $parent = $has_parent_id;
         } else {
             $parent = $category->parent;
         }
         $has_id = woo_sync_gettargetsiteobjid($term_id , 'category');
         if ($has_id) {
             $term_id = $has_id;
         }

		 $has_a_id = woo_sync_gettargetsiteobjid($termmeta['thumbnail_id'][0] , 'media');

		 
		 if (get_the_guid($termmeta['thumbnail_id'][0])){

		if ($has_a_id){
		 $images =  array(
				              'id'=> $has_a_id
	               );
		}else{

          if (!get_option("woo_sync_is_local_host")){
		   $images =  array(
				              'src'=> get_the_guid($termmeta['thumbnail_id'][0])
	               );
		   }else{			
		 $images =  array(
				              'src'=> get_option('woo_sync_url_cdn') . basename(get_the_guid($termmeta['thumbnail_id'][0]))
	               );
		   }
        }		 
		 
		 }else{
		 $images = null;	 
		 }
		 
		 if ($termmeta['display_type'][0]){
			 $display = $termmeta['display_type'][0];
		 }else{
			 $display = 'default';
		 }

		 
		 $parametrs = array(
                 'name' => $category->name,
                 'slug' => $category->slug,
                 'parent' => $parent,
                 'description' => $category->description,
                 'display' => $display,
				 'image' => $images
             );
		
          if (get_option('woo_sync_cat_immediately') == 'on'){
		if ($has_a_id == ''){	
			 $is_local_host = get_option("woo_sync_is_local_host");
			 if ($is_local_host){		
		 $res = upload_category_image_before_publish($termmeta['thumbnail_id'][0]);
			 }
		}			  
		  woo_sync_rest_api_category_handle( $parametrs , $term_id , 'edit');
          }else{
		  woo_sync_future_publish_category( $parametrs , $term_id , 'edit'); 
          }
		  
     }
	 }
	 


	 
     add_action('delete_product_cat', 'woo_sync_delete_category' , 10 , 4);
     function woo_sync_delete_category($term_id , $tt_id , $cach , $obj)
     {
		if (!get_option('woo_sync_creating_started')){
         $category = get_term($term_id, 'product_cat');
         $has_id   = woo_sync_gettargetsiteobjid($term_id , 'category');
         if ($has_id) {
             $term_id = $has_id;
         }

		 $parametrs = $cach;
		 
          if (get_option('woo_sync_cat_immediately') == 'on'){			 
		  woo_sync_rest_api_category_handle($parametrs , $term_id , 'delete');
		  }else{
		  woo_sync_future_publish_category( $parametrs , $term_id , 'delete'); 
          }
		  
     }
	 }
	 
	 
 
	 function upload_category_image_before_publish($file){

       $result = 'Down';
                  $settings   = array(
                      'host' => get_option('woo_sync_server_ip'),
                      'port' => get_option('woo_sync_server_port'),
                      'user' => get_option('woo_sync_ftp_user'),
                      'pass' => get_option('woo_sync_ftp_pass'),
                      'cdn' => get_option('woo_sync_url_cdn'),
                      'path' => get_option('woo_sync_server_path')
                  );
                  $connection = ftp_connect($settings['host'], $settings['port']);
                  $login      = ftp_login($connection, $settings['user'], $settings['pass']);
                  ftp_pasv($connection, true);
                  if (!$connection || !$login)
                      {
                      die(__('woo sync can not connect to your server , Connection failed , please check ftp settings!', 'woocommerce-synchronizer'));
                      }	   
	        global $wpdb;
            $item_detail = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_future_publish where object_type = 'file' and object_id = $file ", ARRAY_A);
            if ($item_detail['id']){			
                  $param  = json_decode($item_detail['parametrs']);
                  $upload = ftp_put($connection, $param->remote_file, $param->local_file, FTP_BINARY); // put the files		  
                  
                  if ($upload)
                      {
                      $result   = 'Down';
                      $wherei['id']   = $item_detail['id'];
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result   = 'fail';
					  die(__('woo sync can not upload media to your server , Connection failed , please check ftp settings!', 'woocommerce-synchronizer'));
                      }
			}

                  ftp_close($connection);
                  return $result;

     } 

?>