<?php


     function woo_sync_future_publish_product($parametrs ,$post , $action , $product_id){
		
	   	 global $wpdb;
         
		 $product  = wc_get_product((int)$post->ID);
		 
	      $insert2['object_type'] = 'product';
	      $insert2['request'] = $post->ID;			  
		  $insert2['date'] = current_time('Y-m-d h:i:s');		 
		 
    	if ($action === 'create'){
			
          $insert2['object_id'] = $post->ID;	  
          $insert2['object_name'] = $product->get_name();	
	      $insert2['action'] = 'create';
          $insert2['parametrs'] = json_encode($parametrs);	
		  
		  if ($product->get_type() === 'variable') {
          woo_sync_create_variantation_product($post, $post->ID , 'create');
          }else{
          update_post_meta($post->ID, 'woo_sync_first_publish', true);
          }		   
		 
		 }elseif ($action === 'edit'){
          $insert2['object_id'] = $product_id;	  
          $insert2['object_name'] = $product->get_name();
	      $insert2['action'] = 'edit';	  
          $insert2['parametrs'] = json_encode($parametrs);	
		  
		  if ($product->get_type() === 'variable')
          woo_sync_create_variantation_product($post, $product_id, 'edit');		  
		 
		 }elseif ($action === 'trash'){
         
   		  $insert2['object_id'] = $product_id;	  
          $insert2['object_name'] = $parametrs['name'];	
	      $insert2['action'] = 'trash';			  
		 
		 }elseif ($action === 'delete'){
          
		  $insert2['object_id'] = $product_id;	  
          $insert2['object_name'] = $parametrs['name'];
	      $insert2['action'] = 'delete';		  
		 
		 }else{
          
		  $insert2['object_id'] = $product_id;	  
          $insert2['object_name'] = $parametrs['name'];	
	      $insert2['action'] = 'untrash';		  
          $insert2['parametrs'] = json_encode($parametrs);		  
		 }			 
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	

	}	
	 
	 
	 
	 
		/*
        @ Publish products changes to target store
		@ this function is called when user delete , trash , create or edit a product in localhost when immediate changes is ENABLED for products
		*/	 
	 function woo_sync_rest_api_product_handle($parametrs ,$post , $action , $product_id , $kind){

	     global $wpdb; 
		 
		 if (is_object($post)){
         $product  = wc_get_product($post->ID);
         }
		 
		 $woosync = new Woo_sync_rest_api();
		 $woosynctarget = new Woo_sync_target_rest_api();
		 $has_access_code = get_option('woo_sync_target_access_code');
         $tools   = new woo_sync_tools();
          $check_parametrs = json_decode(json_encode($parametrs));   
		  $premium_msg = 'In free version , You Can Create / Edit / Trash or Delete only "simple Products"! to manage Variable / External or grouped products you should use premium version <a class="woo-sync-premium" href="https://www.codester.com/items/11458/woocommerce-store-management-by-localhost?ref=sjafarhosseini007" target="_blank">Upgrade to Premium Version</a>';
		  if ($check_parametrs->type === 'simple'){		  
		 if ($action === 'create'){             
				 $api_response   =  $woosync->post('products' , $parametrs);
             if (!is_wp_error($api_response)) {
                 $body           = wp_remote_retrieve_body($api_response);
                 $product_object = json_decode(woo_sync_json_handler($body));

                 delete_post_meta($post->ID, 'woo_sync_Wordpress_error');
                 if (isset($product_object->code) and isset($product_object->message)) {
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result_error', 'Error on creating product : ' . $product_object->code . ' = ' . $product_object->message);
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result');
                     delete_post_meta($post->ID, 'woo_sync_first_publish'); 
                 } else {
					 woo_sync_inserttargetsiteobjid($post->ID , $product_object->id , 'product');
                         if ($has_access_code and isset($product_object->id)){
                            $woosynctarget->post('create' , $product_object->id);
						 }

						 if ($kind === 'now'){
							$parametrs = json_decode(json_encode($parametrs));
						 }						 

						 if (!empty($product_object->images)){						 
						 $target_media = $product_object->images;
						 $local_media = $parametrs->images;
						 $key = 0;
						 foreach($target_media as $med){
                           if (isset($local_media[$key]->src)){
						   $path = explode('uploads/', $med->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media[$key]->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $med->id , 'media');						   
						   }
						   $key++;
                         }
						} 
                     if ($product->get_type() === 'variable' and $kind === 'now') {
                         woo_sync_create_variantation_product($post, $product_object->id, 'create');
                     } else {
                         update_post_meta($post->ID, 'woo_sync_create_product_now_result', 'Product has been published successfully');
                         delete_post_meta($post->ID, 'woo_sync_create_product_now_result_error');
                         update_post_meta($post->ID, 'woo_sync_first_publish', true);
                     }

                               $insert2['dl_id']       = $product_object->id;
                               $insert2['object_type'] = 'product';
                               $insert2['creator'] = 'Myself';							   
							   $insert2['object_id'] = $post->ID;
                               $insert2['rel_type']    = $product_object->type;
                               $insert2['parametrs']   = json_encode($product_object);
                               $insert2['status']   = 'Down';							   
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2); 					 
                
				 }
				 
             } else {
                 update_post_meta($post->ID, 'woo_sync_Wordpress_error', $api_response->get_error_message());
             }
			 		 
			 
			 
		 }
		 
		 
		 
		 
		 if ($action === 'edit'){
             $api_response   =  $woosync->put('products/' . $product_id , $parametrs);
	   
             if (!is_wp_error($api_response)) {                 
				 $body           = wp_remote_retrieve_body($api_response);
                 $product_object = json_decode(woo_sync_json_handler($body));
                 delete_post_meta($post->ID, 'woo_sync_Wordpress_error');
                 if (isset($product_object->code) and isset($product_object->message)) {
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result_error', 'Error on Editing product : ' . $product_object->code . ' = ' . $product_object->message);
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result');
                     delete_post_meta($post->ID, 'woo_sync_first_publish');
                 } else {
					 
                         if ($has_access_code and isset($product_id)){
                           $woosynctarget->post('edit' , $product_id);					 
						 } 
						 
						 if ($kind === 'now'){
							$parametrs = json_decode(json_encode($parametrs));
						 }

						 if (!empty($product_object->images)){							 
						 $target_media = $product_object->images;
						 $local_media = $parametrs->images;
						 $key = 0;
						 foreach($target_media as $med){
                           if (isset($local_media[$key]->src)){
						   $path = explode('uploads/', $med->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media[$key]->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $med->id , 'media');
						   }
						   $key++;
                         }
						 }
						 
                     if ($product->get_type() === 'variable' and $kind === 'now') {
                         woo_sync_create_variantation_product($post, $product_id, 'edit');
                     } else {
                         update_post_meta($post->ID, 'woo_sync_create_product_now_result', 'Product has been Edited successfully');
                         delete_post_meta($post->ID, 'woo_sync_create_product_now_result_error');
                         update_post_meta($post->ID, 'woo_sync_first_publish', true);
                     }
                 }
             } else {
                 update_post_meta($post->ID, 'woo_sync_Wordpress_error', $api_response->get_error_message());
             }	 
		 }
		 
		 
		 
		 if ($action === 'trash'){
             $api_response   =  $woosync->delete('products/' . $product_id , $parametrs , '' );

	         $response = json_decode(woo_sync_json_handler($api_response['body']));	
	         if (isset($response->id)){			 
                         if ($has_access_code and isset($product_id))
                            $woosynctarget->post('trash' , $product_id);
             }						
		 }
		 
		 
		 if ($action === 'untrash'){
             $api_response   =  $woosync->put('products/' . $product_id , $parametrs);
			 
              $row = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}posts WHERE post_parent = '".$post->ID."' and post_type LIKE 'product_variation' ORDER BY id DESC");

		      if ($row){
                  foreach ($row  as $dd) {
					$var_id = woo_sync_gettargetsiteobjid($dd->ID , 'product_variation');
                    $api_response_variations    =  $woosync->put('products/' . $product_id . '/variations/' . $var_id , array('status' => 'publish') , '' );
		          }				 
		      }

	         $response = json_decode(woo_sync_json_handler($api_response['body']));	
	         if (isset($response->id)){				  
                         if ($has_access_code and isset($product_id))
                            $woosynctarget->post('untrash' , $product_id);
			 }
		 
		 }		 
		 
		 
		 if ($action === 'delete'){		 
             $api_response   =  $woosync->delete('products/' . $product_id , $parametrs , '&force=true' );

	         $response = json_decode(woo_sync_json_handler($api_response['body']));	
	         if (isset($response->id)){				 
                         if ($has_access_code and isset($product_id))
                            $woosynctarget->post('delete' , $product_id);
	         }
         woo_sync_deletetargetsiteobjid($product_id , 'product'); 			 
		 }

		 return woo_sync_json_handler($api_response['body']);
		 
                 }else{
                  update_post_meta($post->ID, 'woo_sync_create_product_now_result_error', $premium_msg);
				  $res['message'] = $premium_msg;
				  return json_encode($res);
				 }			 
		 
	 }	
	 

     function woo_sync_future_publish_product_variation($parametrs ,$post , $action , $product_id , $var_id){
		
	   	 global $wpdb;
         $product  = wc_get_product($post->ID);

		 if ($action){
          $insert2['rel_id'] = $product_id;			
          $insert2['object_id'] = $var_id;	  
	      $insert2['object_type'] = 'product_variation';
		  $insert2['date'] = current_time('Y-m-d h:i:s');
	      $insert2['request'] = $post->ID;		  
		 }
		 
		 
    	if ($action === 'create'){	  
	     
  		  $insert2['action'] = 'create';
          $insert2['parametrs'] = json_encode($parametrs);	
         update_post_meta($post->ID, 'woo_sync_first_publish', true);	
         delete_post_meta($post->ID, 'woo_sync_variations_ids');		  
		 
		 }elseif ($action === 'edit'){	
	     
		  $insert2['action'] = 'edit';
          $insert2['parametrs'] = json_encode($parametrs);		  
		 
         }elseif ($action === 'delete'){
			 
	      $insert2['action'] = 'delete';	  
		  

		  }else{
           //nothing here
		  }		  
	 
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	

	}	
	 
	 
	 
		/*
        @ Publish product variations changes to Target store
		@ this function is called when user delete  , create or edit a product variation in localhost when immediate changes IS ENABLED for products
		*/	 
	 
	 function woo_sync_rest_api_product_variation_handle($parametrs ,$post , $action , $product_id , $var_id){


         $product  = wc_get_product($post->ID);
		 $woosync = new Woo_sync_rest_api();
		 
		 if ($action === 'create'){

             $api_response   =  $woosync->post('products/' . $product_id . '/variations' , $parametrs);
			 
             if (!is_wp_error($api_response)) {
                 $body           = wp_remote_retrieve_body($api_response);
                 $product_object = json_decode($body);
                 if (isset($product_object->code) and isset($product_object->message)) {
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result_error', 'Error on creating product variations : ' . $product_object->code . ' = ' . $product_object->message);
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result');
                     delete_post_meta($post->ID, 'woo_sync_first_publish');
                 } else {
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result', 'Product has been published successfully');
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result_error');
                     delete_post_meta($post->ID, 'woo_sync_variations_ids');
                     update_post_meta($post->ID, 'woo_sync_first_publish', true);	

					 woo_sync_inserttargetsiteobjid($var_id , $product_object->id , 'product_variation');

							$parametrs = json_decode(json_encode($parametrs));
						
						 
						 if (!empty($product_object->image)){
						 $target_media = $product_object->image;
						 $local_media = $parametrs->image;
                           if (isset($local_media->src)){
						   $path = explode('uploads/', $target_media->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $target_media->id , 'media');						   
						   }					 
						 }
					           global $wpdb;
                               $insert2['dl_id']       = $product_object->id;					 
                               $insert2['object_id']   = $var_id;
                               $insert2['creator'] = 'Myself';							   
                               $insert2['object_type'] = 'product_variation';
                               $insert2['rel_id']      = $product_id;
                               $insert2['status']      = 'Down';								   
                               $insert2['parametrs']   = json_encode($product_object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);					 
                 } 
				 
			 }	 
		 }
		 
		 
		 
		 
		 if ($action === 'edit'){
             $api_response   =  $woosync->put('products/' . $product_id . '/variations/' . $var_id , $parametrs);
	   
             if (!is_wp_error($api_response)) {                 
				 $body           = wp_remote_retrieve_body($api_response);
                 $product_object = json_decode($body);
                 if (isset($product_object->code) and isset($product_object->message)) {
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result_error', 'Error on editing product variations : ' . $product_object->code . ' = ' . $product_object->message);
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result');
                 } else {


							$parametrs = json_decode(json_encode($parametrs));
						 	
						 if (!empty($product_object->image)){						 
						 $target_media = $product_object->image;
						 $local_media = $parametrs->image;
                           if (isset($local_media->src)){
						   $path = explode('uploads/', $target_media->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $target_media->id , 'media');
						   }							 
						 }
                     update_post_meta($post->ID, 'woo_sync_create_product_now_result', 'Product has been edited successfully');
                     delete_post_meta($post->ID, 'woo_sync_create_product_now_result_error');
                 }
			 }	 
		 }

		 
		 if ($action === 'delete'){		 				
             $api_response   =  $woosync->delete('products/' . $product_id . '/variations/' . $var_id  , $parametrs , '&force=true' );
	         $response = json_decode($api_response['body']);	
	         if (isset($response->id)){					 
                delete_option('woo_sync_deleted_var'.$post->ID);	
			 }	
         woo_sync_deletetargetsiteobjid($var_id , 'product_variation');			 
		 }
		 
		 return woo_sync_json_handler($api_response['body']);	 
	 }	




    function woo_sync_future_publish_attributes( $parametrs , $object_id , $action){
		
		global $wpdb;
		
          $insert2['object_id'] = $object_id;	  
          $insert2['object_name'] = $parametrs['name'];
	      $insert2['object_type'] = 'attribute';			
		  $insert2['date'] = current_time('Y-m-d h:i:s');
		  
    	if ($action === 'create'){	
	      $insert2['action'] = 'create';
          $insert2['parametrs'] = json_encode($parametrs);	
		  
		 }elseif($action === 'edit'){	
	      $insert2['action'] = 'edit';
          $insert2['parametrs'] = json_encode($parametrs);			  
		 
		 }else{	
	      $insert2['action'] = 'delete'; 	  
		 }
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	

	}	
	 
	 

	 function woo_sync_rest_api_attributes_handle($parametrs , $object_id , $action){

		 $woosync = new Woo_sync_rest_api();	 
           $tools   = new woo_sync_tools();
          // woo_sync_checkcache();
		 if ($action === 'create'){
         $api_response   =  $woosync->post('products/attributes' , $parametrs);
		     $api_response['body'] = woo_sync_json_handler($api_response['body']);		 
         $body         = json_decode($api_response['body']);

		 woo_sync_inserttargetsiteobjid($object_id , $body->id , 'attribute');
		  
         global $wpdb;
                               $insert2['dl_id']       = $body->id;
                               $insert2['object_type'] = 'attribute';
                               $insert2['creator'] = 'Myself';							   
                               $insert2['object_id']   = $object_id;
                               $insert2['status'] = 'Down';							   
                               $insert2['parametrs']   = json_encode($body);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);		 
		 }
		 
		 if ($action === 'edit'){
         $api_response   =  $woosync->put('products/attributes/'.$object_id , $parametrs);
		     $api_response['body'] = woo_sync_json_handler($api_response['body']);		 
		 }
		 		 
		 if ($action === 'delete'){
         $api_response   =  $woosync->delete('products/attributes/'.$object_id , $parametrs , '&force=true');
		 $api_response['body'] = woo_sync_json_handler($api_response['body']);		 
         woo_sync_deletetargetsiteobjid($object_id , 'attribute'); 
		 }
		 
		 return $api_response['body'];
		 	 
	 }	 



		
    function woo_sync_future_publish_att_term( $parametrs , $object_id , $action , $rel_id , $tax){
		
		global $wpdb;
		
          $insert2['object_id'] = $object_id;	
          $insert2['rel_id'] = $rel_id;		
          $insert2['rel_name'] = $tax;			  
          $insert2['object_name'] = $parametrs['name'];
	      $insert2['object_type'] = 'attribute_term';			
	      $insert2['action'] = $action; 
          $insert2['parametrs'] = json_encode($parametrs);	
		  $insert2['date'] = current_time('Y-m-d h:i:s');	
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	
	}	
	 

	 function woo_sync_rest_api_att_term_handle($parametrs , $object_id , $action , $rel_id){
		 
		 $woosync = new Woo_sync_rest_api();	 
           $tools   = new woo_sync_tools();
           //woo_sync_checkcache();		 
		 if ($action === 'create'){
                 $api_response   =  $woosync->post('products/attributes/' . $rel_id . '/terms' , $parametrs);
                 $body         = json_decode($api_response['body']);
		         woo_sync_inserttargetsiteobjid($object_id , $body->id , 'att_terms');				 
		 }
		 
		 if ($action === 'edit'){
                 $api_response   =  $woosync->put('products/attributes/' . $rel_id . '/terms/'.$object_id , $parametrs);	 
		 }
		 	 
		 if ($action === 'delete'){
                 $api_response   =  $woosync->delete('products/attributes/' . $rel_id . '/terms/'.$object_id , $parametrs , '&force=true');
                 woo_sync_deletetargetsiteobjid($object_id , 'att_terms'); 				 
		 }
		 
		 return $api_response['body'];	 
	 }	
	 
	 




    function woo_sync_future_publish_category( $parametrs , $object_id , $action){
		
		global $wpdb;
		
          $insert2['object_id'] = $object_id;	  
	      $insert2['object_type'] = 'category';			
		  $insert2['date'] = current_time('Y-m-d h:i:s');	
		  
		if ($action === 'create'){
          $insert2['object_name'] = $parametrs['name'];			
	      $insert2['action'] = 'create';
          $insert2['parametrs'] = json_encode($parametrs);	
		 }elseif ($action === 'edit'){
          $insert2['object_name'] = $parametrs['name'];  	
	      $insert2['action'] = 'edit';
          $insert2['parametrs'] = json_encode($parametrs);		  
		 }else{ 
          $insert2['object_name'] = $parametrs->name;
	      $insert2['action'] = 'delete'; 	  
		 }
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	

	}	
	 
	 

		
	function woo_sync_rest_api_category_handle($parametrs , $object_id , $action){

		 $woosync = new Woo_sync_rest_api();	 
           $tools   = new woo_sync_tools();
           //woo_sync_checkcache();
		 if ($action === 'create'){
         $api_response   =  $woosync->post('products/categories' , $parametrs);
		 $api_response['body'] = woo_sync_json_handler($api_response['body']);
		 $body         = json_decode($api_response['body']);
		 woo_sync_inserttargetsiteobjid($object_id , $body->id , 'category');		 

							$parametrs = json_decode(json_encode($parametrs));		 

						 if (!empty($body->image)){							
						 $target_media = $body->image;
						 $local_media = $parametrs->image;
                           if (isset($local_media->src)){
						   $path = explode('uploads/', $target_media->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $target_media->id , 'media');
						   }	 
						 }
                               global $wpdb;
                               $insert2['dl_id']       = $body->id;
                               $insert2['object_type'] = 'category';
                               $insert2['creator'] = 'Myself';							   
                               $insert2['object_id']   = $object_id;
                               $insert2['status'] = 'Down';							   
                               $insert2['parametrs']   = json_encode($body);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);			 
		 }
		 
		 if ($action === 'edit'){
         $api_response   =  $woosync->put('products/categories/'.$object_id , $parametrs);
		 $api_response['body'] = woo_sync_json_handler($api_response['body']);	
         $body = json_decode($api_response['body']);

							$parametrs = json_decode(json_encode($parametrs));

						 if (!empty($body->image)){							
						 $target_media = $body->image;
						 $local_media = $parametrs->image;
                           if (isset($local_media->src)){
						   $path = explode('uploads/', $target_media->src);
						   $real_path = dirname($path[1]).'/'.basename($local_media->src);
						   $local_id = woo_sync_get_image_id_by_name($real_path);
					       woo_sync_inserttargetsiteobjid($local_id , $target_media->id , 'media');
						   }
						 }
		 
		 }
		 	 
		 if ($action === 'delete'){
         $api_response   =  $woosync->delete('products/categories/'.$object_id , $parametrs , '&force=true');
		 $api_response['body'] = woo_sync_json_handler($api_response['body']);		 
		  
		  global $wpdb;
          woo_sync_deletetargetsiteobjid($object_id , 'category'); 
          }
		 
		 

		 
		 return $api_response['body'];
		 		 
	 }





	 
    function woo_sync_future_publish_tags( $parametrs , $object_id , $action){
		
		global $wpdb;
		
          $insert2['object_id'] = $object_id;	  
          $insert2['object_name'] = $parametrs['name'];
	      $insert2['object_type'] = 'tag';		
	      $insert2['action'] = $action;
          $insert2['parametrs'] = json_encode($parametrs);		  
		  $insert2['date'] = current_time('Y-m-d h:i:s');		  
		 
		$sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);	

	}	
	 
	 

		
	 function woo_sync_rest_api_tags_handle($parametrs , $object_id , $action){
		 
		 $woosync = new Woo_sync_rest_api();	 
           $tools   = new woo_sync_tools();
          // woo_sync_checkcache();	 
		 if ($action === 'create'){
             $api_response   =  $woosync->post('products/tags' , $parametrs);
		     $api_response['body'] = woo_sync_json_handler($api_response['body']);			 
             $body         = json_decode($api_response['body']);	
			woo_sync_inserttargetsiteobjid($object_id , $body->id , 'tag');
         
		                       global $wpdb;
                               $insert2['dl_id']       = $body->id;
                               $insert2['object_type'] = 'tag';
                               $insert2['creator'] = 'Myself';							   
                               $insert2['object_id']   = $object_id;
                               $insert2['status'] = 'Down';							   
                               $insert2['parametrs']   = json_encode($body);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);				 
		 }
		 
		 if ($action === 'edit'){
         $api_response   =  $woosync->put('products/tags/'.$object_id , $parametrs);
		     $api_response['body'] = woo_sync_json_handler($api_response['body']);		 
		 }		 
		 
		 if ($action === 'delete'){
         $api_response   =  $woosync->delete('products/tags/'.$object_id , $parametrs , '&force=true');
		     $api_response['body'] = woo_sync_json_handler($api_response['body']);	
         woo_sync_deletetargetsiteobjid($object_id , 'tag'); 			 
		 }
		 
		 return $api_response['body'];	 
	 }	 	 
?>