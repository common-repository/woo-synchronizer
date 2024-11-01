<?php
 

       add_action('wp_ajax_nopriv_woo_sync_preapering_downloads', 'woo_sync_preapering_downloads');
       add_action('wp_ajax_woo_sync_preapering_downloads', 'woo_sync_preapering_downloads');
       function woo_sync_preapering_downloads() {

           $tools   = new woo_sync_tools();
           $woosync = new Woo_sync_rest_api();
	   
           $tools->checknetwork('loseInternetConnection' , 'str');

           /////////empty text file		 
           $tools->logresults(__("Downloading new object...", 'woocommerce-synchronizer'), 'w');
           //woo_sync_checkcache();
           
           global $wpdb;
           delete_option('woo_sync_products_ids');
		   $sant_type = sanitize_text_field($_POST['otype']);
		   if (isset($sant_type)){
           $type = $sant_type;
		   }else{
			   $type = '';
		   }
           $check_db = array();
           $end = '';
           $page = 1;


           //////tax
           if ($type == 'tax') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'tax' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $check_db[$db] = $row->rel_type;
                   $db++;
               }
               
               $i = 0;
               
               $tools->logresults(__("Submit a Request for Tax Classes...", 'woocommerce-synchronizer'), 'a');
               

                   
                   $api_response = $woosync->get('taxes/classes', '', '&per_page=100');
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       
                       foreach ($objects as $object) {
                           if (in_array(urldecode($object->slug) , $check_db)) {
                               ///maybe update in next version           
                           } else {
							   $insert2['rel_type'] = urldecode($object->slug);
                               $insert2['object_type'] = 'tax';
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       
                       
                       $page++;
                       
                   } else {
                    $tools->checknetwork('loseInternetConnection' , 'str');
                   }
                   
               if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("tax classes found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("tax classe found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           

		   
           //////tax
           if ($type == 'shipping') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'shipping_classes' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $check_db[$db] = $row->dl_id;
                   $db++;
               }
               
               $i = 0;
               
               $tools->logresults(__("Submit a Request for Shipping Classes...", 'woocommerce-synchronizer'), 'a');
               

                   $api_response = $woosync->get('products/shipping_classes', '', '&per_page=100');
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       if (empty($objects)) {
                           update_option('woo_sync_last_page_of_shipping', $page);
                           $end = "ended";
                       }
                       
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'shipping_classes';
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       
                       
                       $page++;
                       
                   } else {
                    $tools->checknetwork('loseInternetConnection' , 'str');
                   }
                   
               if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("shipping classes found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("shipping class found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }		   
		   
           //////tag
           if ($type == 'tag') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'tag' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $check_db[$db] = $row->dl_id;
                   $db++;
               }
               
               $i = 0;
               
               $tools->logresults(__("Submit a Request for Products Tags...", 'woocommerce-synchronizer'), 'a');
               
               do {
                   
                   $api_response = $woosync->get('products/tags', '', '&per_page=100&page=' . $page);
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       if (empty($objects)) {
                           update_option('woo_sync_last_page_of_tag', $page);
                           $end = "ended";
                       }
                       
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'tag';
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       
                       
                       $page++;
                       
                   } else {
                    $tools->checknetwork('loseInternetConnection' , 'str');
                   }
                   
               } while ($end != "ended");
			   
			   if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("tags found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("tag found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           
           //////category	 
           
           if ($type == 'category') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               
               $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'category' ORDER BY id DESC");
               $db   = 0;
			   $cleari = 0;
               foreach ($rows as $row) {
                   $check_db[$db] = $row->dl_id;
                   $db++;
               }
               
               $i = 0;
               
               
               $tools->logresults(__("Submit a Request for Products categories...", 'woocommerce-synchronizer'), 'a');
               
               do {
                   
                   $api_response = $woosync->get('products/categories', '', '&per_page=100&page=' . $page);
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       if (empty($objects)) {
                           update_option('woo_sync_last_page_of_category', $page);
                           $end = "ended";
                       }
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'category';
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       
                       $page++;
                   } else {
                               $tools->checknetwork('loseInternetConnection' , 'str');
                   }
               } while ($end != "ended");
			   if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("categories found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("category found.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           //////attribute
           
           
           
           if ($type == 'attribute') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'attribute' ORDER BY id DESC");
               $db   = 0;
               $cleari = 0;			   
               foreach ($rows as $row) {
                   $check_db[$db] = $row->dl_id;
                   $db++;
               }
               
               
               $i = 0;
               
               $tools->logresults(__("Submit a Request for Products attributes...", 'woocommerce-synchronizer'), 'a');
               
               do {
                   
                   $api_response = $woosync->get('products/attributes', '', '');
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'attribute';
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       $end = "ended";
                   } else {
                               $tools->checknetwork('loseInternetConnection' , 'str');
                   }
               } while ($end != "ended");
			   
			   if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("attributes found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("attribute found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           
           
           
           
           
           //////products
           
           
           
           if ($type == 'product') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product' ORDER BY id DESC");
               $db   = 0;
               $cleari = 0;			   
               foreach ($rows as $row) {
                   $check_db[$db] = $row->dl_id;
                   $db++;
               }
               
               $i = 0;
               
               
               $tools->logresults(__("Submit a Request for Products...", 'woocommerce-synchronizer'), 'a');
               
               do {
                   
                   $api_response = $woosync->get('products/', '', '&per_page=50&page=' . $page);
                   
                   $wp_check_errors = is_object($api_response);
                   if ($wp_check_errors == 0) {
                       $objects = json_decode($api_response['body']);
                       if (empty($objects)) {
                           $end = "ended";
                       }
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
							   if ($object->type === 'simple'){
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'product';
                               $insert2['rel_type']    = $object->type;
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
							   }
                           }
                           $i++;
                           
                           $tools->logresults($object->name . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                       
                       $page++;
                       
                   } else {
                               $tools->checknetwork('loseInternetConnection' , 'str');
                   }
               } while ($end != "ended");
			   if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("Products were found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("Product found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           
           
           
           
           if ($type == 'product_variation') {
               
               $tools->logresults(__("Checking Download history...", 'woocommerce-synchronizer'), 'a');
               
               $rows0 = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product_variation' ORDER BY id DESC");
               $db    = 0;
               $i     = 0;
               $cleari = 0;			   
               foreach ($rows0 as $row0) {
                   $check_db[$db] = $row0->dl_id;
                   $db++;
               }
               
               $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product' and rel_type = 'variable' ORDER BY id DESC");
               $db   = 0;
               
               
               $tools->logresults(__("Submit a Request for Products variations...", 'woocommerce-synchronizer'), 'a');
               
               foreach ($rows as $row) {
                   $api_response = $woosync->get('products/' . $row->dl_id . '/variations', '', '&per_page=100');
                   
                   $wp_check_errors_term = is_object($api_response);
                   if ($wp_check_errors_term == 0) {
                       $objects = json_decode($api_response['body']);
                       foreach ($objects as $object) {
                           if (in_array($object->id, $check_db)) {
                               ///maybe update in next version           
                           } else {
                               $insert2['dl_id']       = $object->id;
                               $insert2['object_type'] = 'product_variation';
                               $insert2['rel_id']      = $row->dl_id;
                               $insert2['parametrs']   = json_encode($object);
                               $insert2['date']        = current_time('Y-m-d h:i:s');
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_download_list', $insert2);
                           }
                           $i++;
                           
                           $tools->logresults(__("variation", 'woocommerce-synchronizer') . ' ' . $object->id . '📥', 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           $cleari++;
                       }
                   } else {
                               $tools->checknetwork('loseInternetConnection' , 'str');
                   }
               }
               
			   if ($i > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("variations found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
			   die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($i) .' '. __("variation found...", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }
           }
           
           
           
           die('<br><br><hr>*************** Creating... ***************<hr><br><br>');
           
       }
       
       
       
       
       add_action('wp_ajax_nopriv_woo_sync_start_downloads', 'woo_sync_start_downloads');
       add_action('wp_ajax_woo_sync_start_downloads', 'woo_sync_start_downloads');
       function woo_sync_start_downloads() {

           $tools = new woo_sync_tools();	
           $woosync = new Woo_sync_rest_api();		   
           $tools->checknetwork('loseInternetConnection' , 'str');
           
           global $wpdb;
		   $sant_otype = sanitize_text_field($_POST['otype']);
		   if (isset($sant_otype)){
           $type = $sant_otype;
		   }else{
			   $type = '';
		   }
           

           
           $tools->logresults(__("Creating new object...", 'woocommerce-synchronizer'), 'w');
           update_option("woo_sync_creating_started", true);
           
           
           if ($type == 'tag') {
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'tag' and status = '' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $param           = json_decode($row->parametrs);
                   $created         = wp_insert_term($param->name, 'product_tag', array(
                       'slug' => $param->slug,
                       'description' => $param->description
                   ));
                   $wp_check_errors = is_object($created);
                   if ($wp_check_errors == 0) {
                      // add_term_meta($created['term_id'], 'another_woo_product_tag_id' . $created['term_id'], $param->id);
                       woo_sync_inserttargetsiteobjid($created['term_id'] , $param->id , 'tag');
					   $db++;
                       $cleari++;
                       $update['status']    = 'Down';
                       $update['object_id'] = $created['term_id'];
                       $where['id']         = $row->id;
                       $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
                       
                       ////writing to log
                       $tools->logresults($param->name . ' ' . __("is created successfully", 'woocommerce-synchronizer'), 'a');
                       
                       if ($cleari > 100) {
                           $tools->logresults('********', 'w');
                           $cleari = 0;
                       }
                       
                   } else {
                       $tools->logerrors(__("Failed to create tag", 'woocommerce-synchronizer') . ' *' . $param->name . '*<br>  Error : ' . $created->get_error_message(), 'a');
                   }
               }
               
               if ($db > 1){
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Product tags Created", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Product tag Created", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           

           if ($type == 'tax') {
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'tax' and status = '' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $param           = json_decode($row->parametrs);
				   if ($param->slug != 'standard'){
                   $all_taxes = get_option('woocommerce_tax_classes');

                   $new_tax =$param->name;

                   $tax_list = explode(PHP_EOL, $all_taxes);
				   $new_tax_list = array();
                   foreach($tax_list as $trimed){
                   $new_tax_list[] = trim($trimed);
                   }
		           if (array_search($new_tax , $new_tax_list) === false){
		               array_push($new_tax_list , $new_tax);
		           }
		 
		           $new_tax_classes =  implode(PHP_EOL , $new_tax_list);
                   update_option('woocommerce_tax_classes' , $new_tax_classes);
				   }
					   $db++;
                       $cleari++;
                       $update['status']    = 'Down';
                       $where['id']         = $row->id;
                       $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
                       
                       ////writing to log
                       $tools->logresults($param->name . ' ' . __("is created successfully.", 'woocommerce-synchronizer'), 'a');
                       
                       if ($cleari > 100) {
                           $tools->logresults('********', 'w');
                           $cleari = 0;
                       }
                       

               
               }
               if ($db > 1){
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Tax classes created", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Tax class created", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }






		   
           if ($type == 'shipping') {
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'shipping_classes' and status = '' ORDER BY id DESC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $param           = json_decode($row->parametrs);
                   $created         = wp_insert_term($param->name, 'product_shipping_class', array(
                       'slug' => $param->slug,
                       'description' => $param->description
                   ));
                   $wp_check_errors = is_object($created);
                   if ($wp_check_errors == 0) {
                      // add_term_meta($created['term_id'], 'another_woo_product_tag_id' . $created['term_id'], $param->id);
                       woo_sync_inserttargetsiteobjid($created['term_id'] , $param->id , 'shipping_classes');
					   $db++;
                       $cleari++;
                       $update['status']    = 'Down';
                       $update['object_id'] = $created['term_id'];
                       $where['id']         = $row->id;
                       $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
                       
                       ////writing to log
                       $tools->logresults($param->name . ' ' . __("is created successfully.", 'woocommerce-synchronizer'), 'a');
                       
                       if ($cleari > 100) {
                           $tools->logresults('********', 'w');
                           $cleari = 0;
                       }
                       
                   } else {
                       $tools->logerrors(__("Failed to create shipping class", 'woocommerce-synchronizer') . ' *' . $param->name . '*<br>  Error : ' . $created->get_error_message(), 'a');
                   }
               }
               
               if ($db > 1){
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Shipping classes created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
				wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("shipping class created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');   
			   }
           }		   
		   
           
           
           
           if ($type == 'category') {
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'category' and status = '' ORDER BY id ASC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   
                   
                  $tools->checknetwork('loseInternetConnection' , 'str');
                   
                   
                   $param  = json_decode($row->parametrs);
                   $parent = 0;
				   
                   if ($param->parent != 0) {
                         $parent =  woo_sync_getthissiteobjid($param->parent , 'category');
                   }
                   
                   $created         = wp_insert_term($param->name, 'product_cat', array(
                       'slug' => $param->slug,
                       'description' => $param->description,
                       'parent' => $parent
                   ));
                   $wp_check_errors = is_object($created);
                   if ($wp_check_errors == 0) {
                      // add_term_meta($created['term_id'], 'another_woo_cat_id' . $created['term_id'], $param->id);
                      // update_option('another_woo_cat_id' . $created['term_id'], $param->id);
					   woo_sync_inserttargetsiteobjid($created['term_id'] , $param->id , 'category');
                       add_term_meta($created['term_id'], 'display_type', $param->display);
                       
					   if (!empty($param->image)){
						   update_option("woo_sync_creating_started", true);
                           $img_id = $tools->getobjectimg($param->image->src , $param->image->id);
                           add_term_meta($created['term_id'], 'thumbnail_id', $img_id);
                       }
					   
                       $update['status']    = 'Down';
                       $update['object_id'] = $created['term_id'];
                       $where['id']         = $row->id;
                       $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
                       $db++;
                       $cleari++;
                       
                       
                       $tools->logresults($param->name . ' ' . __("is created successfully.", 'woocommerce-synchronizer'), 'a');
                       
                       
                       if ($cleari > 100) {
                           $tools->logresults('********', 'w');
                           $cleari = 0;
                       }
                       
                   } else {
                       $tools->logerrors(__("Failed to create category", 'woocommerce-synchronizer') .' *'. $param->name . '*<br> Error : ' . $created->get_error_message(), 'a');
                   }
               }
               
			   if ($db > 1){
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Categories created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               wp_die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Category created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           
           
           
           
           
           
           if ($type == 'attribute') {
               
               
               $site = get_option("woo_sync_target_url");
               $ck   = get_option("woo_sync_customer_key");
               $sk   = get_option("woo_sync_secret_key");
               
               $rows   = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'attribute' and status = '' ORDER BY id ASC");
               $db     = 0;
               $cleari = 0;
               foreach ($rows as $row) {
                   $param = json_decode($row->parametrs);
                   $slug  = explode('pa_', $param->slug);
                   $args  = array(
                       'name' => $param->name,
                       'slug' => $slug[1],
                       'type' => $param->type,
                       'order_by' => $param->order_by,
                       'has_archives' => $param->has_archives
                   );
                   
                   
                   $att_id = wc_create_attribute($args);
                   
                   
                   
                   $wp_check_errors = is_object($att_id);
                   if ($wp_check_errors == 0) {
                     //  update_option('another_woo_att_' . $att_id, $param->id);
					   woo_sync_inserttargetsiteobjid($att_id , $param->id , 'attribute');
                       $db++;
                       $cleari++;
                       
                       
                       
                       
                       $ff = register_taxonomy($param->slug, 'product', array(
                           'label' => $param->name,
                           'rewrite' => array(
                               'slug' => $slug[1]
                           ),
                           'hierarchical' => true
                       ));
                       
                       
                                  $api_response = $woosync->get('products/attributes/' . $param->id . '/terms', '', '');
								  
                       
                       $wp_check_errors_term = is_object($api_response);
                       if ($wp_check_errors_term == 0) {
                           $objects = json_decode($api_response['body']);
                           
                           foreach ($objects as $object) {
                               $created = wp_insert_term($object->name, $param->slug, array(
                                   'slug' => $object->slug,
                                   'description' => $object->description
                               ));
                               add_term_meta($created['term_id'], 'order_' . $param->slug, $object->menu_order);
                              // add_term_meta($created['term_id'], 'another_woo_att_terms_id' . $created['term_id'], $object->id);
                               woo_sync_inserttargetsiteobjid($created['term_id'] , $object->id , 'att_terms');                           
						   }
                           
                           
                           $update['status']    = 'Down';
                           $update['object_id'] = $att_id;
                           $where['id']         = $row->id;
                           $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
                           
                           $tools->logresults($param->name . ' ' . __("created successfully.", 'woocommerce-synchronizer'), 'a');
                           
                           if ($cleari > 100) {
                               $tools->logresults('********', 'w');
                               $cleari = 0;
                           }
                           
                       }
                       
                   } else {
                       $tools->logerrors(__("Failed to create Attribute", 'woocommerce-synchronizer') .' *'. $param->name . '*<br>  Error : ' . $att_id->get_error_message(), 'a');
                   }
               }
               
			   if ($db > 1){
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Attributes created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');
			   }else{
               die('<b style="color : #35d835;" >' . woo_sync_converting_numbers($db) .' '. __("Attribute created.", 'woocommerce-synchronizer') . '</b><br>_________<br><br>');				   
			   }
           }
           
           
           
           
           delete_option("woo_sync_creating_started");
           wp_die('<br><br><hr>******* (+_+) ********<hr><br><br>');
       }
       
       
       
      
       add_action('wp_ajax_nopriv_woo_sync_create_product_with_wp_ajax', 'woo_sync_create_product_with_wp_ajax');
       add_action('wp_ajax_woo_sync_create_product_with_wp_ajax', 'woo_sync_create_product_with_wp_ajax');
       
       function woo_sync_create_product_with_wp_ajax() {
           
           
           $tools = new woo_sync_tools();	   
                     
           
           global $wpdb;
		   $sant_type = sanitize_text_field($_POST['otype']);
		   if (isset($sant_type)){
           $type = $sant_type;
		   }else{
			   $type = '';
		   }
           $data = sanitize_text_field($_POST['data']);
           update_option("woo_sync_creating_started", true);
           
           if ($data == '') {
               $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product' and status = '' ORDER BY id DESC");
               $vg   = 0;
               foreach ($rows as $dbdata) {
                   $dataid[$vg] = $dbdata->id;
                   $vg++;
               }
           } else {
               $dataid = maybe_unserialize(get_option('woo_sync_products_ids'));
           }
           
           
           if ($tools->checknetwork( '' , 'bool')) {
               
			   if (!empty($dataid)){
               $Pid = array_shift($dataid);
               update_option('woo_sync_products_ids', $dataid);
               if (!is_numeric($Pid)) {
                   $Pid = 0;
               }
               $row = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list where id = " . $Pid, ARRAY_A);
               }
           } else {
               
               $jsoner = array(
                   'OK' => __( 'Internet connection is not established...', 'woocommerce-synchronizer' ),
                   'DATA' => 'loseInternetConnection'
               );
               echo json_encode($jsoner);
               die();
               
           }
           
           if (!empty($row)) {
               $param = json_decode($row['parametrs']);
               
               
               if ($param->type === 'variable') {
                   $objProduct = new WC_Product_Variable();
               } elseif ($param->type === 'grouped') {
                   $objProduct = new WC_Product_Grouped();
               } elseif ($param->type === 'external') {
                   $objProduct = new WC_Product_External();
                   $objProduct->set_product_url($param->external_url);
                   $objProduct->set_button_text($param->button_text);
               } else {
                   $objProduct = new WC_Product_Simple();
               }
               
               if ($param->name)
                   $objProduct->set_name($param->name);
               
               if ($param->slug)
                   $objProduct->set_slug($param->slug);
               
               if ($param->status)
                   $objProduct->set_status($param->status);
               
               if ($param->featured)
                   $objProduct->set_featured($param->featured);
               
               if ($param->catalog_visibility)
                   $objProduct->set_catalog_visibility($param->catalog_visibility);
               
               if ($param->description)
                   $objProduct->set_description($param->description);
               
               if ($param->short_description)
                   $objProduct->set_short_description($param->short_description);
               
               if ($param->price)
                   $objProduct->set_price($param->price);
               
               if ($param->regular_price)
                   $objProduct->set_regular_price($param->regular_price);
               
               
               
               
               if ($param->sale_price) {
                   $objProduct->set_sale_price($param->sale_price);
                   $objProduct->set_date_on_sale_from($param->date_on_sale_from);
                   $objProduct->set_date_on_sale_to($param->date_on_sale_to);
               }
               
               if ($param->total_sales)
                   $objProduct->set_total_sales($param->total_sales);
               
               if ($param->tax_status)
                   $objProduct->set_tax_status($param->tax_status);
               
               if ($param->tax_class)
                   $objProduct->set_tax_class($param->tax_class);
               
               if (!$param->virtual) {
                   $objProduct->set_manage_stock($param->manage_stock);
                   $objProduct->set_stock_quantity($param->stock_quantity);
                   $objProduct->set_stock_status($param->stock_status);
                   $objProduct->set_backorders($param->backorders);
               }
               
               if ($param->sold_individually)
                   $objProduct->set_sold_individually($param->sold_individually);
               
               if ($param->weight)
                   $objProduct->set_weight($param->weight);
               
               if ($param->dimensions->length)
                   $objProduct->set_length($param->dimensions->length);
               
               if ($param->dimensions->width)
                   $objProduct->set_width($param->dimensions->width);
               
               if ($param->dimensions->height)
                   $objProduct->set_height($param->dimensions->height);
               
               if ($param->parent_id)
                   $objProduct->set_parent_id($param->parent_id);
               
               if ($param->reviews_allowed)
                   $objProduct->set_reviews_allowed($param->reviews_allowed);
               
               if ($param->purchase_note)
                   $objProduct->set_purchase_note($param->purchase_note);
               
               if (!empty($param->attributes)) {
                   $att_obj = $param->attributes;
                   $key_i   = 0;
                   foreach ($att_obj as $att) {
                       if ($att->id != 0) {

                           $att_id = woo_sync_getthissiteobjid($att->id , 'attribute');
                           if ($att_id) {
                               $new             = array(
                                   'id' => $att_id
                               );
                               $att             = array_replace((array) $att, $new);
                               $att_slug        = wc_attribute_taxonomy_name_by_id($att_id);
                               $atts[$att_slug] = $att;
                           }
                       } else {
                           $att_slug        = 'pa_' . $att->name;
                           $atts[$att_slug] = (array) $att;
                       }
                   }
                   $objProduct->set_attributes(woo_sync_wc_prepare_product_attributes($atts));
               }
               
               
               if (!empty($param->default_attributes)) {
                   $att_obj_def = $param->default_attributes;
                   $key_i       = 0;
                   foreach ($att_obj as $att) {
                       if ($att->id != 0) {
                           $att_id = woo_sync_getthissiteobjid($att->id , 'attribute');
                           if ($att_id) {
                               $new             = array(
                                   'id' => $att_id
                               );
                               $att                 = array_replace((array) $att, $new);
                               $att_slug            = wc_attribute_taxonomy_name_by_id($att_id);
                               $atts_def[$att_slug] = $att;
                           }
                       } else {
                           $att_slug            = 'pa_' . $att->name;
                           $atts_def[$att_slug] = (array) $att;
                       }
                   }
                   $objProduct->set_default_attributes(woo_sync_wc_prepare_product_attributes($atts_def));
               }
               
               
               if ($param->menu_order)
                   $objProduct->set_menu_order($param->menu_order);
               
               if (!empty($param->categories)) {
                   $cat_obj = $param->categories;
                   $key_i   = 0;
                   foreach ($cat_obj as $cat) {
                       if ($cat->id != 0) {
                           $cat_id = woo_sync_getthissiteobjid($cat->id , 'category');
                       }
                       $categories[$key_i] = $cat_id;
                       $key_i++;
                       
                   }
                   $objProduct->set_category_ids($categories);
               }
               
               
               
               if (!empty($param->tags)) {
                   $tag_obj = $param->tags;
                   $key_i   = 0;
                   foreach ($tag_obj as $tag) {
                       
                       if ($tag->id != 0) {
                              $tag_id = woo_sync_getthissiteobjid($tag->id , 'tag');
                       }
                       $tags[$key_i] = $tag_id;
                       $key_i++;
                   }
                   $objProduct->set_tag_ids($tags);
               }
               
               if ($param->virtual)
                   $objProduct->set_virtual($param->virtual);
               
               if ($param->shipping_class_id) {
                   $objProduct->set_shipping_class_id(woo_sync_getthissiteobjid($param->shipping_class_id , 'shipping_classes'));
               }
               
               if ($param->downloadable) {
                   $objProduct->set_downloadable($param->downloadable);
                   $objProduct->set_downloads(json_decode(json_encode($param->downloads), True));
                   $objProduct->set_download_limit($param->download_limit);
                   $objProduct->set_download_expiry($param->download_expiry);
               }
               $gallery_ids = array();
               if (!empty($param->images)) {
				              update_option("woo_sync_creating_started", true);
                   $images_array = $param->images;
                   $main_img     = $images_array[0];
                   $img_id       = $tools->getobjectimg($main_img->src , $main_img->id);
                   $objProduct->set_image_id($img_id);
                   unset($images_array[0]);
                   
                   $key_j = 0;
                   foreach ($images_array as $gallery) {
                       $gallery_ids[$key_j] = $tools->getobjectimg($gallery->src , $gallery->id);
                       $key_j++;
                   }
                   
                   $objProduct->set_gallery_image_ids($gallery_ids);
               }
               
               if ($param->rating_count)
                   $objProduct->set_rating_counts($param->rating_count);
               
               if ($param->average_rating)
                   $objProduct->set_average_rating($param->average_rating);
               
               $new_product_id = $objProduct->save();
               update_post_meta($new_product_id, '_sku', $param->sku);
               update_post_meta($new_product_id, '_stock_status', $param->stock_status);
               
               
               if ($param->type === "variable") {
                   
                   $rows_var = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product_variation' and  rel_id = '" . $row['dl_id'] . "' and status = '' ORDER BY id ASC");
                   foreach ($rows_var as $row0) {
                       $object = json_decode($row0->parametrs);
                       
                       if (!empty($object->attributes)) {
                           $att_obj = $object->attributes;
                           foreach ($att_obj as $att) {
                               if ($att->id != 0) {

                                       $att_id                   = woo_sync_getthissiteobjid($att->id , 'attribute');
                                       $att_slug                 = wc_attribute_taxonomy_name_by_id($att_id);
                                       $option_slug              = get_term_by('name', $att->option, $att_slug)->slug;
                                       $attribute_var[$att_slug] = $option_slug;
                                       // $attribute_var[$att_slug] = $option_slug ;				

                               } else {
                                   $attribute_var[$att->name] = $att->option;
                               }
                           }
                       }
                       
                       $parent_id = $new_product_id;
					   $image_id = '';
                       if (!empty($object->image)){
						$image_id = $tools->getobjectimg($object->image->src , $object->image->id);   
					   }
                       $variation_data = array(
                           'id' => $object->id,
                           'attributes' => $attribute_var,
                           'sku' => $object->sku,						   
                           'description' => $object->description,
                           'price' => $object->price,
                           'status' => $object->status,
                           'regular_price' => $object->regular_price,
                           'sale_price' => $object->sale_price,
                           'date_on_sale_from' => $object->date_on_sale_from,
                           'date_on_sale_to' => $object->date_on_sale_to,
                           'on_sale' => $object->on_sale,
                           'purchasable' => $object->purchasable,
                           'virtual' => $object->virtual,
                           'downloadable' => $object->downloadable,
                           'downloads' => $object->downloads,
                           'download_limit' => $object->download_limit,
                           'download_expiry' => $object->download_expiry,
                           'tax_status' => $object->tax_status,
                           'manage_stock' => $object->manage_stock,
                           'stock_quantity' => $object->stock_quantity,
                           'stock_status' => $object->stock_status,
                           'backorders' => $object->backorders,
                           'backorders_allowed' => $object->backorders_allowed,
                           'backordered' => $object->backordered,
                           'weight' => $object->weight,
                           'length' => $object->dimensions->length,
                           'width' => $object->dimensions->width,
                           'height' => $object->dimensions->height,
                           'shipping_class' => $object->shipping_class,
                           'shipping_class_id' => $object->shipping_class_id,
						   'image' => $image_id,
                           'menu_order' => $object->menu_order
                       );
                       
                       
                       woo_sync_create_product_variation_by_product_id($parent_id, $variation_data, $row0->id);
                       
                   }
                   
                   
               }
               
               

               
               //add_post_meta($new_product_id, 'woo_sync_another_site_pid', $param->id);
			   woo_sync_inserttargetsiteobjid($new_product_id , $param->id , 'product');
               
               if (!empty($param->grouped_products)) {
               $update['grouped']    = maybe_serialize($param->grouped_products);
               }
               
               if (!empty($param->upsell_ids)) {
               $update['upsells']    = maybe_serialize($param->upsell_ids);
               }
               
               if (!empty($param->cross_sell_ids)) {
               $update['scross']    = maybe_serialize($param->cross_sell_ids);
               }
			   
			   
               $update['status']    = 'Down';
               $update['object_id'] = $new_product_id;
               $where['id']         = $row['id'];
               $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);			   
               
               $jsoner = array(
                   'OK' => __("product ", 'woocommerce-synchronizer') . ' *' . $param->name . '* ' . __("created successfully", 'woocommerce-synchronizer'),
                   'DATA' => 'Continue'
               );
               echo json_encode($jsoner);
               die();
               
           } else {
               
             if (!get_option("firststart")){
			   $count = 0;
               if ($data == 'Continue') {
                   $counter0 = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE object_type = 'product' and status = 'Down' ORDER BY id DESC");
                   foreach ($counter0 as $counter0) {
                       $count++;
                   }
               }	 
				 
				 update_option("firststart" , true);
				 if ($count > 1){
		       $jsoner = array(
                   'OKnotconsol' =>  '<b style="color : #35d835;" >'.woo_sync_converting_numbers($count).' '.__("products created.", 'woocommerce-synchronizer').'</b><br>_________<br><br>'.__("Creating cross sells...", 'woocommerce-synchronizer').'<br>',
                   'DATA' => 'Continue'
               );
				 }else{
		       $jsoner = array(
                   'OKnotconsol' =>  '<b style="color : #35d835;" >'.woo_sync_converting_numbers($count).' '.__("product created.", 'woocommerce-synchronizer').'</b><br>_________<br><br>'.__("Creating cross sells...", 'woocommerce-synchronizer').'<br>',
                   'DATA' => 'Continue'
               );					 
				 }
               echo json_encode($jsoner);
               die(); 
			 }
 

             if (!get_option("crosssellscreate")){ 
		$special_cross = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE scross != '' ORDER BY id DESC");
		$pc_i = 0;
		foreach($special_cross as $cross)
			{	
                   foreach (maybe_unserialize($cross->scross) as $pc) {
                       $item_p_c   = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list where dl_id = " . $pc . " ", ARRAY_A);
                       $ids[$pc_i] = $item_p_c['object_id'];
                       $pc_i++;
                   }
                   update_post_meta($cross->object_id, '_crosssell_ids', $ids);
			}		
               update_option("crosssellscreate" , true);			
		       $jsoner = array(
                   'OKnotconsol' =>  '<b style="color : #35d835;" >'.__("Cross-sells created ...", 'woocommerce-synchronizer').'</b><br>_________<br><br>'.__("Creating upsells...", 'woocommerce-synchronizer').'<br>',
                   'DATA' => 'Continue'
               );
               echo json_encode($jsoner);
               die();	
             }  

			 
             if (!get_option("upsellscreate")){ 			 
		$special_upsells = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE upsells != '' ORDER BY id DESC");
		$pc_i = 0;
		foreach($special_upsells as $upsells)
			{	
                   foreach (maybe_unserialize($upsells->upsells) as $pc) {
                       $item_p_c   = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list where dl_id = " . $pc . " ", ARRAY_A);
                       $ids[$pc_i] = $item_p_c['object_id'];
                       $pc_i++;
                   }
                   update_post_meta($upsells->object_id, '_upsell_ids', $ids);
			}	
               update_option("upsellscreate" , true);			
		       $jsoner = array(
                   'OKnotconsol' =>  '<b style="color : #35d835;" >'.__("upsells created...", 'woocommerce-synchronizer').'</b><br>_________<br><br>'.__("Creating Grouped products", 'woocommerce-synchronizer').'<br>',
                   'DATA' => 'Continue'
               );
               echo json_encode($jsoner);
               die();
			 }



             if (!get_option("groupedcreate")){ 			 
		$special_grouped = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE grouped != '' ORDER BY id DESC");
		$pc_i = 0;
		foreach($special_grouped as $grouped)
			{	
                   foreach (maybe_unserialize($grouped->grouped) as $pc) {
                       $item_p_c   = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list where dl_id = " . $pc . " ", ARRAY_A);
                       $ids[$pc_i] = $item_p_c['object_id'];
                       $pc_i++;
                   }
                   update_post_meta($grouped->object_id, '_children', $ids);
			} 
               update_option("groupedcreate" , true);			
		       $jsoner = array(
                   'OKnotconsol' => '<b style="color : #35d835;" >'.__("Grouped products created...", 'woocommerce-synchronizer').'<b><br>_________<br><br>',
                   'DATA' => 'Continue'
               );
               echo json_encode($jsoner);
               die();
			 }
               
               

               
               delete_option('woo_sync_products_ids');
               delete_option("woo_sync_creating_started");

               delete_option('firststart');
               delete_option("crosssellscreate");
               delete_option('upsellscreate');
               delete_option("groupedcreate");               
               $jsoner = array(
                   'OK' => '💪😎👍' . __("All Down!", 'woocommerce-synchronizer').'<br> But In free version , You Can just Download "simple Products"! to Download Variable / External or grouped products you should use premium version . <a class="woo-sync-premium" href="https://www.codester.com/items/11458/woocommerce-store-management-by-localhost?ref=sjafarhosseini007" target="_blank">Upgrade to Premium Version</a>',
                   'DATA' => 'Down'
               );
               
               echo json_encode($jsoner);
               die();
               
               
               
               
           }
           
       }
       
       
       
         
       add_action('wp_ajax_nopriv_woo_sync_delete_error_log', 'woo_sync_delete_error_log');
       add_action('wp_ajax_woo_sync_delete_error_log', 'woo_sync_delete_error_log');
       function woo_sync_delete_error_log() {
           $myerror = fopen(plugin_dir_path(__FILE__) . 'error.txt', "w") or die("Unable to open file!");
           $txt = '';
           fwrite($myerror, $txt);
           fclose($myerror);
           
           $myfile = fopen(plugin_dir_path(__FILE__) . 'results.txt', "w") or die("Unable to open file!");
           $txt = '';
           fwrite($myfile, $txt);
           fclose($myfile);
           
           die();
       }
       
       
       
    
       add_action('wp_ajax_nopriv_woo_sync_delete_All_Downloaded_files', 'woo_sync_delete_All_Downloaded_files');
       add_action('wp_ajax_woo_sync_delete_All_Downloaded_files', 'woo_sync_delete_All_Downloaded_files');
       function woo_sync_delete_All_Downloaded_files() {
           global $wpdb;
           $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}woo_sync_download_list");
        //   $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}woo_sync_relationships");		   
           die();
       }
       
       
       

       function woo_sync_create_product_variation_by_product_id($product_id, $variation_data, $rowid) {
           // Get the Variable product object (parent)
           $product = wc_get_product($product_id);
           
           $variation_post = array(
               'post_title' => $product->get_title(),
               'post_name' => 'product-' . $product_id . '-variation',
               'post_status' => $variation_data['status'],
               'post_parent' => $product_id,
               'post_type' => 'product_variation',
               'guid' => $product->get_permalink()
           );
           
           
           // Creating the product variation
           $variation_id = wp_insert_post($variation_post);
           
           global $wpdb;
           $update['status']    = 'Down';
           $update['object_id'] = $variation_id;
           $where['id']         = $rowid;
           $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_download_list', $update, $where);
           
           //update_post_meta($variation_id, 'woo_sync_another_site_var_id', $variation_data['id']);
		   woo_sync_inserttargetsiteobjid($variation_id , $variation_data['id'] , 'product_variation');           
           // Get an instance of the WC_Product_Variation object
           $variation = new WC_Product_Variation($variation_id);
           // Iterating through the variations attributes
           foreach ($variation_data['attributes'] as $attribute => $term_name) {
               $taxonomy = $attribute; // The attribute taxonomy
               update_post_meta($variation_id, 'attribute_' . $taxonomy, $term_name);
           }
           
           ## Set/save all other data
           if ($variation_data['description'])
		   $variation->set_description($variation_data['description']);

           if ($variation_data['price'])	   
           $variation->set_price($variation_data['price']);
	   
           if ($variation_data['regular_price'])	   
           $variation->set_regular_price($variation_data['regular_price']);
	   
           if ($variation_data['sale_price'])	   
           $variation->set_sale_price($variation_data['sale_price']);
           
           if (!empty($variation_data['on_sale'])) {
               $variation->set_date_on_sale_from($variation_data['date_on_sale_from']);
               $variation->set_date_on_sale_to($variation_data['date_on_sale_to']);
           }
           
           if ($variation_data['virtual'])		   
           $variation->set_virtual($variation_data['virtual']);
	   
           if ($variation_data['downloadable'])	   
           $variation->set_downloadable($variation_data['downloadable']);
	   
           if ($variation_data['downloads'])	   
           $variation->set_downloads($variation_data['downloads']);
	   
           if ($variation_data['download_limit'])	   
           $variation->set_download_limit($variation_data['download_limit']);
	   
           if ($variation_data['download_expiry'])	   
           $variation->set_download_expiry($variation_data['download_expiry']);
	   
           if ($variation_data['tax_status'])	   
           $variation->set_tax_status($variation_data['tax_status']);
           
           if (!empty($variation_data['stock_quantity'])) {
               $variation->set_manage_stock($variation_data['manage_stock']);
               $variation->set_stock_quantity($variation_data['stock_quantity']);
               $variation->set_stock_status($variation_data['stock_status']);
           }
           
           if ($variation_data['backorders'])		   
           $variation->set_backorders($variation_data['backorders']);
	   
           if ($variation_data['weight'])	   
           $variation->set_weight($variation_data['weight']);
	   
           if ($variation_data['length'])	   
           $variation->set_length($variation_data['length']);
	   
           if ($variation_data['width'])	   
           $variation->set_width($variation_data['width']);
	   
           if ($variation_data['height'])	   
           $variation->set_height($variation_data['height']);
	   
           if ($variation_data['shipping_class_id'])	   
           $variation->set_shipping_class_id(woo_sync_getthissiteobjid($variation_data['shipping_class_id'] , 'shipping_classes'));
	   
           if ($variation_data['image'])			   
           $variation->set_image_id($variation_data['image']);
	   
           if ($variation_data['menu_order'])	   
           $variation->set_menu_order($variation_data['menu_order']);
           
           $new_var_id = $variation->save(); // Save the data
           update_post_meta($new_var_id, '_sku', $variation_data['sku']);
           
       }
       
       
       
       
       
       
       /// a function to prepare product attributes while creating products      
       function woo_sync_wc_prepare_product_attributes($attributes) {
           global $woocommerce;
           
           $data     = array();
           $position = 0;
           
           foreach ($attributes as $taxonomy => $values) {
               if (!taxonomy_exists($taxonomy)) {
                   $attribute = new WC_Product_Attribute();
                   
                   $term_ids = array();
                   
                   // Loop through the term names
                   foreach ((array) $values['options'] as $term_name) {
                       $term_ids[] = $term_name;
                   }
                   $named = explode('pa_', $taxonomy);
                   $attribute->set_id(0);
                   $attribute->set_name($named[1]);
                   $attribute->set_options($term_ids);
                   $attribute->set_position($position);
                   $attribute->set_visible($values['visible']);
                   $attribute->set_variation($values['variation']);
                   
                   
               } else {
                   
                   // Get an instance of the WC_Product_Attribute Object
                   $attribute = new WC_Product_Attribute();
                   
                   $term_ids = array();
                   
                   // Loop through the term names
                   foreach ($values['options'] as $term_name) {
                       if (term_exists($term_name, $taxonomy))
                       // Get and set the term ID in the array from the term name
                           $term_ids[] = get_term_by('name', $term_name, $taxonomy)->term_id;
                       else
                           continue;
                   }
                   
                   $taxonomy_id = wc_attribute_taxonomy_id_by_name($taxonomy); // Get taxonomy ID
                   
                   $attribute->set_id($taxonomy_id);
                   $attribute->set_name($taxonomy);
                   $attribute->set_options($term_ids);
                   $attribute->set_position($position);
                   $attribute->set_visible($values['visible']);
                   $attribute->set_variation($values['variation']);
               }
               
               
               $data[$taxonomy] = $attribute; // Set in an array
               
               $position++; // Increase position
           }
           return $data;
       }
       
?>