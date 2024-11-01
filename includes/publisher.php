<?php


		
          add_action('wp_ajax_nopriv_woo_sync_delete_future_list', 'woo_sync_delete_future_list');
          add_action('wp_ajax_woo_sync_delete_future_list', 'woo_sync_delete_future_list');
          function woo_sync_delete_future_list()
              {
              $id    = sanitize_text_field($_POST['id']);
              $kind  = sanitize_text_field($_POST['kind']);
              $objid = sanitize_text_field($_POST['objid']);
              global $wpdb;
              
              $wherei['id'] = $id;
              $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
              
              $row = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_future_publish WHERE rel_id = '$objid' ORDER BY id DESC");
              $i   = 0;
              foreach ($row as $row)
                  {
                  if ($kind == 'product' and $row->object_type == 'product_variation')
                      {
                      $wherei['id'] = $row->id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  
                  if ($kind == 'attribute' and $row->object_type == 'attribute_term')
                      {
                      $wherei['id'] = $row->id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  }
              }
          
          
 
         
          add_action('wp_ajax_nopriv_woo_sync_future_list_elements', 'woo_sync_future_list_elements');
          add_action('wp_ajax_woo_sync_future_list_elements', 'woo_sync_future_list_elements');
          function woo_sync_future_list_elements()
              {
              $tools = new woo_sync_tools();
              if ($tools->checknetwork('loseInternetConnection', 'bool'))
                  {
                  }
              else
                  {
                  die(__( 'Internet connection is not established...', 'woocommerce-synchronizer' ));
                  }
              global $wpdb;
              
              $row     = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_future_publish ORDER BY id DESC");
              $i       = 0;
              $j       = 0;
              $listed0 = array();
              foreach ($row as $row)
                  {
                  if ($row->object_type == 'product_variation')
                      {
                      $listed0[$j] = $row->id;
                      $j++;
                      }
                  else
                      {
                      $listed[$i] = $row->id;
                      $i++;
                      }
                  
                  }
              $ar3 = array_merge($listed0, $listed);
              echo json_encode($ar3);
              die();
              }
          
          
          
          
          
          
          
          add_action('wp_ajax_nopriv_woo_sync_future_list_publisher', 'woo_sync_future_list_publisher');
          add_action('wp_ajax_woo_sync_future_list_publisher', 'woo_sync_future_list_publisher');
          function woo_sync_future_list_publisher()
              {
              global $wpdb;
              $result['OK']   = '';
              $result['info'] = '';
              
              $id = 0;
			  $sant_id = sanitize_text_field($_POST['id']);
              if (isset($sant_id))
                  {
                  $id = $sant_id;
                  }
              
              $item_detail = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_future_publish where id = '$id' ", ARRAY_A);
              
              switch ($item_detail['action'])
              {
                  case "create":
                      $action = __("Created", 'woocommerce-synchronizer');
					  $del_action = __("Create", 'woocommerce-synchronizer');
                      break;
                  case "edit":
                      $action = __("Edited", 'woocommerce-synchronizer');
					  $del_action = __("Edit", 'woocommerce-synchronizer');					  
                      break;
                  case "delete":
                      $action = __("Deleted", 'woocommerce-synchronizer');
					  $del_action = __("Delete", 'woocommerce-synchronizer');					  
                      break;
                  case "trash":
                      $action = __("Trashed", 'woocommerce-synchronizer');
					  $del_action = __("Trash", 'woocommerce-synchronizer');					  
                      break;
                  case "untrash":
                      $action = __("Untrashed", 'woocommerce-synchronizer');
					  $del_action = __("Untrash", 'woocommerce-synchronizer');					  
                      break;
              }
              
              
              if ($item_detail['object_type'] == 'attribute')
                  {
				  
                 $has_id = woo_sync_gettargetsiteobjid($item_detail['object_id'] , 'attribute');
                 if ($has_id) {
                    $att_id = $has_id;
                 }else{
                    $att_id = $item_detail['object_id'];
				 }				 
				  
                  $response = json_decode(woo_sync_rest_api_attributes_handle(json_decode($item_detail['parametrs']), $att_id  , $item_detail['action']));
                  
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
                      $result['info'] = '<br><b style="color : green;" >'. $item_detail['object_name'] .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
                      $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' . $item_detail['object_name'] . '</b><br>
		              ' . __("Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      }
                  
                  }
              
              
              
              
              if ($item_detail['object_type'] == 'tag')
                  {
                  
                  $has_id = woo_sync_gettargetsiteobjid($item_detail['object_id'] , 'tag');
                  if ($has_id)
                      {
                      $term_id = $has_id;
                      }
                  else
                      {
                      $term_id = $item_detail['object_id'];
                      }
                  
                  $response = json_decode(woo_sync_rest_api_tags_handle(json_decode($item_detail['parametrs']), $term_id, $item_detail['action']));
                  
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
                      $result['info'] = '<br><b style="color : green;" >'. $item_detail['object_name'] .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
                      $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' . $item_detail['object_name'] . '</b><br>
		              ' . __("Store Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      }
                  
                  }
              
              
              
              
              if ($item_detail['object_type'] == 'category')
                  {
                  
                  $has_id = woo_sync_gettargetsiteobjid($item_detail['object_id'] , 'category');
                  if ($has_id)
                      {
                      $term_id = $has_id;
                      }
                  else
                      {
                      $term_id = $item_detail['object_id'];
                      }
                  
                  
                  $response = json_decode(woo_sync_rest_api_category_handle(json_decode($item_detail['parametrs']), $term_id, $item_detail['action']));
                  
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
                     $result['info'] = '<br><b style="color : green;" >'. $item_detail['object_name'] .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
                      $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' . $item_detail['object_name'] . '</b><br>
		              ' . __("Store Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      }
                  
                  }
              
              
              
              if ($item_detail['object_type'] == 'attribute_term')
                  {
                  
                  $has_id = woo_sync_gettargetsiteobjid($item_detail['object_id'] , 'att_terms');
                  if ($has_id)
                      {
                      $term_id = $has_id;
                      }
                  else
                      {
                      $term_id = $item_detail['object_id'];
                      }
                  
                  $has_att_id = woo_sync_gettargetsiteobjid($item_detail['rel_id'] , 'attribute');
                  if ($has_att_id)
                      {
                      $attributesid = $has_att_id;
                      }
                  else
                      {
                      $attributesid = $item_detail['rel_id'];
                      }
                  
                  $response = json_decode(woo_sync_rest_api_att_term_handle(json_decode($item_detail['parametrs']), $term_id, $item_detail['action'], $attributesid));
                  
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
                      $result['info'] = '<br><b style="color : green;" >'. $item_detail['object_name'] .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
                      $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' . $item_detail['object_name'] . '</b><br>
		              ' . __("Store Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      }
                  
                  }
              
              
              
              
              if ($item_detail['object_type'] == 'product')
                  {
                  
                  $another_id = woo_sync_gettargetsiteobjid($item_detail['object_id'] , 'product');
                  if ($another_id)
                      {
                      $product_id = $another_id;
                      }
                  else
                      {
                      $product_id = $item_detail['object_id'];
                      }
                  
                  $post     = get_post($item_detail['request']);
                  $body     = woo_sync_rest_api_product_handle(json_decode($item_detail['parametrs']), $post, $item_detail['action'], $product_id, 'future');
                  $response = json_decode(woo_sync_json_handler($body));
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
                      $result['info'] = '<br><b style="color : green;" >'. $item_detail['object_name'] .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
                      $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' . $item_detail['object_name'] . '</b><br>
		              ' . __("Store Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      }
                  
                  }
              
              
              
              
              
              if ($item_detail['object_type'] == 'product_variation')
                  {
                  
                  $objid = $item_detail['rel_id'];
                  
                  $another_id = woo_sync_gettargetsiteobjid($item_detail['rel_id'] , 'product');
                  if ($another_id)
                      {
                      $product_id = $another_id;
                      }
                  else
                      {
                      $product_id = $item_detail['rel_id'];
                      }
                  
                  $post = get_post($item_detail['request']);
                  
                  if ($item_detail['action'] != 'delete')
                      {
                      $body = woo_sync_rest_api_product_variation_handle(json_decode($item_detail['parametrs']), $post, $item_detail['action'], $product_id, $item_detail['object_id']);
                      }
                  else
                      {
                      $body = woo_sync_rest_api_product_variation_handle(json_decode($item_detail['parametrs']), $item_detail['request'], $item_detail['action'], $product_id, $item_detail['object_id']);
                      }
                  $response = json_decode(woo_sync_json_handler($body));
                  if (isset($response->id))
                      {
                      $result['OK']   = 'Down';
					  $result['info'] = '<br><b style="color : green;" >'.__("variation of product", 'woocommerce-synchronizer').' '.get_the_title($objid) .' '.  $action .' '. __("successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'Fail';
					  $result['info'] = '<br><b style="color : red;" >' .__("Failed to", 'woocommerce-synchronizer') .' '. $del_action . ' ' .__("variation of product", 'woocommerce-synchronizer').' '. get_the_title($objid) . '</b><br>
	             	  ' . __("Store Response : ", 'woocommerce-synchronizer') . ' ' . $response->message . '<hr>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);					  
                      }
                  
                  }
              
              
              
              if ($item_detail['object_type'] == 'file')
                  {
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
                      die(__("woo sync can not connect to your server , Connection failed , please check ftp settings!", 'woocommerce-synchronizer'));
                      }
                  
                  $param  = json_decode($item_detail['parametrs']);
                  $upload = ftp_put($connection, $param->remote_file, $param->local_file, FTP_BINARY); // put the files		  
                  
                  if ($upload)
                      {
                      $result['OK']   = 'Down';
                      $result['info'] = '<br><b style="color : green;" >' . $item_detail['object_name'] . ' ' . __("Uploaded Successfully", 'woocommerce-synchronizer') . '</b><br>';
                      $wherei['id']   = $id;
                      $wpdb->delete($wpdb->prefix . 'woo_sync_future_publish', $wherei);
                      }
                  else
                      {
                      $result['OK']   = 'fail';
                      $result['info'] = '<br><b style="color : red;" >' . $item_detail['object_name'] . ' ' . __("Can not upload!", 'woocommerce-synchronizer') . '</b><br>';
                      }
                  
                  ftp_close($connection);
                  
                  }
              
              die(json_encode($result));
              }
			  
			  



		
          add_action('wp_ajax_nopriv_woo_sync_future_list_delete_server_files', 'woo_sync_future_list_delete_server_files');
          add_action('wp_ajax_woo_sync_future_list_delete_server_files', 'woo_sync_future_list_delete_server_files');
          function woo_sync_future_list_delete_server_files()
              {
             $is_local_host = get_option("woo_sync_is_local_host");
			 if ($is_local_host){ 
                  $settings   = array(
                      'host' => get_option('woo_sync_server_ip'),
                      'port' => get_option('woo_sync_server_port'),
                      'user' => get_option('woo_sync_ftp_user'),
                      'pass' => get_option('woo_sync_ftp_pass'),
                      'cdn' => get_option('woo_sync_url_cdn'),
                      'path' => get_option('woo_sync_server_path'),
                  );
                  $connection = ftp_connect($settings['host'], $settings['port']);
                  $login      = ftp_login($connection, $settings['user'], $settings['pass']);
                  ftp_pasv($connection, true);
                  if (!$connection || !$login)
                      {
                      die('woo sync can not connect to your server , Connection failed , please check ftp settings!');
                      }
					  
                      $logs_dir = $settings['path'];
					  if ($logs_dir != ''){
                      ftp_chdir($connection, $logs_dir);
                      $files = ftp_nlist($connection, ".");
                      foreach ($files as $file)
                      {
                       ftp_delete($connection, $file);
                      }  					
                      }					  
				die('<h3 style="color:green">All Down!</h3>');	  
              }else{
                die('<br> All Down!');
			  }		

			  }			  
			  
?>