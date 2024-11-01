<?php
     /*
     @create product and product's variation
     @update product and product's variation
     @delete product and product's variation
     */
	 
	 

     function woo_sync_create_new_product($post, $action)
     {
	if (!get_option('woo_sync_creating_started')){ 
	      
         $is_local_host = get_option("woo_sync_is_local_host");
         global $product;
         $product  = wc_get_product($post->ID);
         ////////imagelinks    
		 $thumnail_id = get_post_thumbnail_id($post->ID);
         $feauture = wp_get_attachment_image_src( $thumnail_id , 'full');
		 $images_link = array();
         
		 if ($feauture) {
        $another_media = woo_sync_gettargetsiteobjid($thumnail_id , 'media');			 
		if ($another_media){
		 $images_link[0] =  array(
				              'id'=> $another_media
	               );
		}else{
			
             if ($is_local_host) {
                 $fea_url = get_option('woo_sync_url_cdn') . basename($feauture['0']);
             } else {
                 $fea_url = $feauture['0'];
             }
             $images_link[0] = array(
                 'src' => $fea_url
             );
			 
		}
		 }
 

        $attachment_ids = $product->get_gallery_image_ids();
		$product_img_gallery_posted = sanitize_text_field($_POST['product_image_gallery']);
		if (empty($attachment_ids) and isset($product_img_gallery_posted)){
			if ($product_img_gallery_posted != ''){
		        $attachment_ids = explode(',' , $product_img_gallery_posted);
            }		
		}else{
		$attachment_ids = $product->get_gallery_image_ids();	
		}
		if (!empty($attachment_ids)){
		$id = 1;	
        foreach ($attachment_ids as $attachment_id) {
		$another_media = woo_sync_gettargetsiteobjid($attachment_id , 'media');		 
		if ($another_media){
		 $images_link[$id] =  array(
				              'id'=> $another_media
	               );
		}else{				 
				 
                 if ($is_local_host) {
                     $determedimage = get_option('woo_sync_url_cdn') . basename(wp_get_attachment_url($attachment_id));
                 } else {
                     $determedimage = wp_get_attachment_url($attachment_id);
                 }
                 $images_link[$id] = array(
                     'src' => $determedimage
                 );                 
             }
		$id++;	 
		} 
       }
         ///////categories	
         $categories_id = $product->get_category_ids();
		 $product_cats = array();
         if ($categories_id) {
             $i = 0;
             foreach ($categories_id as $categories_id) {
                 $has_id = woo_sync_gettargetsiteobjid($categories_id , 'category');
                 if ($has_id) {
                     $product_cats[$i] = array(
                         'id' => $has_id
                     );
                 } else {
                     $product_cats[$i] = array(
                         'id' => $categories_id
                     );
                 }
                 $i++;
             }
         }
         //////attributes	
         $list_product_att = (array) $product->get_attributes();
		 $sorted_atrribute = array();
         $j                = 0;
         foreach ($list_product_att as $att_data) {
             $wcp    = $att_data->get_data();
             $ss     = 0;
             $wcp_id = woo_sync_gettargetsiteobjid($wcp['id'] , 'attribute');
             if ($wcp_id) {
                 $att_id = $wcp_id;
             } else {
                 $att_id = $wcp['id'];
             }
             $terms = $wcp['options'];
             foreach ($terms as $options) {
                 $wc_options       = get_term_by('id', $options, $wcp['name']);
				 if ($wc_options !== false){
                 $att_options[$ss] = $wc_options->name;
                 $ss++;
				 }
             }
			 
             if ($wcp['is_taxonomy']) {
                 $sorted_atrribute[$j] = array(
                     'id' => $att_id,
                     'position' => $wcp['position'],
                     'visible' => $wcp['is_visible'],
                     'variation' => $wcp['is_variation'],
                     'options' => $att_options
                 );
             } else {
                 $sorted_atrribute[$j] = array(
                     'name' => $wcp['name'],
                     'position' => $wcp['position'],
                     'visible' => $wcp['visible'], // default: false
                     'variation' => $wcp['is_variation'],
                     'options' => $wcp['value']
                 );
             }
             $j++;
         }
         ////tags
		 $sorted_tags = array();
         $list_tags = $product->get_tag_ids();
         $t         = 0;
         foreach ($list_tags as $tag_data) {
             $wct        = get_term_by('id', $tag_data, 'product_tag');
             $has_tag_id = woo_sync_gettargetsiteobjid($wct->term_id , 'tag');
             if ($has_tag_id) {
                 $tag_id = $has_tag_id;
             } else {
                 $tag_id = $wct->term_id;
             }
             $sorted_tags[$t] = array(
                 'id' => $tag_id,
                 'name' => $wct->name,
                 'slug' => $wct->slug
             );
             $t++;
         }
         /////$files
		 $files = array();
         $list_downloads = $product->get_downloads();
         $m              = 0;
         foreach ($list_downloads as $download) {
             $ddata = $download->get_data();
             if ($is_local_host) {
                 $determedfile = get_option('woo_sync_url_cdn') . basename($ddata['file']);
             } else {
                 $determedfile = $ddata['file'];
             }
             $files[$m] = array(
                 'id' => $ddata['id'],
                 'name' => $ddata['name'],
                 'file' => $determedfile
             );
             $m++;
         }

         $backorder = $product->get_backorders();
         if ($backorder == '') {
             $backorder = 'no';
         }
         $on_sale_date_from = $product->get_date_on_sale_from();
         $on_sale_date_to   = $product->get_date_on_sale_to();
		 $date_from = '';
		 $date_to = '';
         if ($on_sale_date_from) {
             $date_from = $on_sale_date_from->date("Y-m-d");
         }
         if ($on_sale_date_to) {
             $date_to = $on_sale_date_to->date("Y-m-d");
         }
         $rl         = 0;
		 $upsales = array();
         $upsales_id = $product->get_upsell_ids();
         foreach ($upsales_id as $upsales_id) {
             $replacer = woo_sync_gettargetsiteobjid($upsales_id, 'product');
             if ($replacer) {
                 $upsales[$rl] = $replacer;
             } else {
                 $upsales[$rl] = $upsales_id;
             }
             $rl++;
         }
		 $crosssales = array();
         $rl2      = 0;
         $cross_id = $product->get_cross_sell_ids();
         foreach ($cross_id as $cross_id) {
             $replacer = woo_sync_gettargetsiteobjid($cross_id, 'product');
             if ($replacer) {
                 $crosssales[$rl2] = $replacer;
             } else {
                 $crosssales[$rl2] = $cross_id;
             }
             $rl2++;
         }
		 $grouped = array();
         $rl3         = 0;
         $grouped_ids = $product->get_children();
         foreach ($grouped_ids as $grouped_ids) {
             $replacer = woo_sync_gettargetsiteobjid($grouped_ids, 'product');
             if ($replacer) {
                 $grouped[$rl3] = $replacer;
             } else {
                 $grouped[$rl3] = $grouped_ids;
             }
             $rl3++;
         }
		 $p_parent = '';
         $product_parent = woo_sync_gettargetsiteobjid($product->get_parent_id() , 'product');
         if ($product_parent) {
             $p_parent = $product_parent;
         } else {
             $p_parent = $product->get_parent_id();
         }
		 $product_url = '';
         if ($product->get_type() == 'external') {
             $product_url = $product->get_product_url();
         }
		 	
          $product_shipping_class_posted = sanitize_text_field($_POST['product_shipping_class']);
         if (isset($product_shipping_class_posted)){			
		 $shippin_class_id = woo_sync_gettargetsiteobjid($product_shipping_class_posted , 'shipping_classes');
		 }else{
		 $shippin_class_id = woo_sync_gettargetsiteobjid($product->get_shipping_class_id() , 'shipping_classes');			 
		 }
		 
		 if ($shippin_class_id){
			 if (isset($product_shipping_class_posted)){
			 $s_c = get_term($product_shipping_class_posted , 'product_shipping_class' , ARRAY_A);
			 $shipping_class = $s_c['slug'];
			 }else{
			 $shipping_class = 	$product->get_shipping_class(); 
			 }
		 }else{
			 $shipping_class = '';
		 }
		 
		 
		 $posted_tax = sanitize_text_field($_POST['_tax_class']);
		 if (isset($posted_tax)){
		 $tax_class = $posted_tax;
		 }else{
		 $tax_class = $product->get_tax_class();			 
		 }
		 
         $parametrs = array(
             'images' => $images_link,
             'name' => $product->get_name(),
             'slug' => $product->get_slug(),
             'type' => $product->get_type(),
             'status' => $product->get_status(),
             'price' => $product->get_price(),
             'regular_price' => $product->get_regular_price(),
             'sale_price' => $product->get_sale_price(),
             'featured' => $product->get_featured(),
             'catalog_visibility' => $product->get_catalog_visibility(),
             'description' => $product->get_description(),
             'short_description' => $product->get_short_description(),
             'sku' => $product->get_sku(),
             'date_on_sale_from' => $date_from,
             'date_on_sale_to' => $date_to,
             'on_sale' => $product->is_on_sale(),
             'purchasable' => $product->is_purchasable(),
             'total_sales' => $product->get_total_sales(),
             'virtual' => $product->is_virtual(),
             'downloadable' => $product->is_downloadable(),
             'downloads' => $files,
             'download_limit' => $product->get_download_limit(),
             'download_expiry' => $product->get_download_expiry(),
             'external_url' => $product_url,
             'button_text' => $product->single_add_to_cart_text(),
             'tax_status' => $product->get_tax_status(),
             'tax_class' => $tax_class,
             'manage_stock' => $product->get_manage_stock(),
             'stock_quantity' => $product->get_stock_quantity(),
             'stock_status' => $product->get_stock_status(),
             'backorders' => $backorder,
             'backorders_allowed' => $product->backorders_allowed(),
             'sold_individually' => $product->get_sold_individually(),
             'weight' => $product->get_weight(),
             'dimensions' => array(
                 'length' => $product->get_length(),
                 'width' => $product->get_width(),
                 'height' => $product->get_height()
             ),
             'shipping_required' => $product->needs_shipping(),
             'shipping_taxable' => $product->is_shipping_taxable(),
             'shipping_class' => $shipping_class,
             'shipping_class_id' => $shippin_class_id,
             'reviews_allowed' => $product->get_reviews_allowed(),
             'average_rating' => $product->get_average_rating(),
             'rating_count' => $product->get_rating_counts(),
             'upsell_ids' => $upsales,
             'cross_sell_ids' => $crosssales,
             'grouped_products' => $grouped,
             'parent_id' => $p_parent,
             'purchase_note' => $product->get_purchase_note(),
             'categories' => $product_cats,
             'tags' => $sorted_tags,
             'attributes' => $sorted_atrribute,
             'menu_order' => $product->get_menu_order()
         );
		 
          if ($action == 'create') {
            if (get_post_meta($post->ID, 'woo_sync_create_product_now', true)) {	  
                woo_sync_rest_api_product_handle($parametrs ,$post , $action , $post->ID ,'now');
			}else{
				woo_sync_future_publish_product($parametrs ,$post , $action ,$post->ID);		
			}
         }
		 
	  
         if ($action == 'edit') {
             $replacer = woo_sync_gettargetsiteobjid($post->ID , 'product');
             if ($replacer) {
                 $product_id = $replacer;
             } else {
                 $product_id = $post->ID;
             }
            if (get_post_meta($post->ID, 'woo_sync_create_product_now', true)) {	  
                woo_sync_rest_api_product_handle($parametrs ,$post , $action , $product_id , 'now');			
			}else{
				woo_sync_future_publish_product($parametrs ,$post , $action , $post->ID);				
			}			 

         }
     }
	 }	 
	 
	 
	 
	 

     function woo_sync_create_variantation_product($post, $product_id, $action)
     {
		 if (!get_option('woo_sync_creating_started')){		 
         $is_local_host = get_option("woo_sync_is_local_host");
         global $product;
         $product  = wc_get_product($post->ID);
         $list     = $product->get_available_variations();
         $old_vars = get_post_meta($post->ID, 'woo_sync_variations_ids');

		 $oldest_variations = array();		 
		 if (isset($old_vars[0])){
		 $oldest_variations = $old_vars[0];	 
		 }

         $sp       = 0;
         foreach (array_reverse($list) as $dd) {
             $other_meta = get_post_meta($dd['variation_id']);
			 $var_product  = wc_get_product($dd['variation_id']);
             //print_r($other_meta);
             if ($other_meta['_sale_price_dates_from'][0]) {
                 $sale_price_from                = date('Y-m-d', (int) $other_meta['_sale_price_dates_from'][0]);
                 $parametrs['date_on_sale_from'] = $sale_price_from;
             } else {
                 $parametrs['date_on_sale_from'] = '';
             }
             if ($other_meta['_sale_price_dates_to'][0]) {
                 $sale_price_to                = date('Y-m-d', (int) $other_meta['_sale_price_dates_to'][0]);
                 $parametrs['date_on_sale_to'] = $sale_price_to;
             } else {
                 $parametrs['date_on_sale_to'] = '';
             }
             if ($other_meta['_regular_price'][0]) {
                 $parametrs['regular_price'] = $other_meta['_regular_price'][0];
             }
             if ($other_meta['_sale_price'][0]) {
                 $parametrs['sale_price'] = $other_meta['_sale_price'][0];
             }
             if (isset($other_meta['_downloadable_files'][0]) and $other_meta['_downloadable_files'][0] != 'a:0:{}') {
				 $files = array();
                 $list_downloads = maybe_unserialize($other_meta['_downloadable_files'][0]);
                 $m              = 0;
                 foreach ($list_downloads as $download) {
                     $ddata = maybe_unserialize($download);
                     if ($is_local_host) {
                         $determedfile = get_option('woo_sync_url_cdn') . basename($ddata['file']);
                     } else {
                         $determedfile = $ddata['file'];
                     }
                     $files[$m] = array(
                         'id' => $ddata['id'],
                         'name' => $ddata['name'],
                         'file' => $determedfile
                     );
                     $m++;
                 }
                 if ($files) {
                     $parametrs['downloads'] = $files;
                 }
             } else {
                 $parametrs['downloads'] = array();
             }
			 
			 
			 
////////////image of variation			 
             if ($dd['image']['url']) {
         $att_img_id = woo_sync_get_image_id($dd['image']['url']);
         $att_a_id = woo_sync_gettargetsiteobjid($att_img_id , 'media');
		if ($att_a_id){
		 $parametrs['image'] =  array(
				              'id'=> $att_a_id
	               );
		}else{				 
				 
                 if ($is_local_host) {
                     $determedimage = get_option('woo_sync_url_cdn') . basename($dd['image']['url']);
                 } else {
                     $determedimage = $dd['image']['url'];
                 }
                 $parametrs['image'] = array(
                     'src' => $determedimage
                 );
				 
		}
		
		
		             }
/////////////////////////////////////////////////

		
		

             if ($other_meta['_download_limit'][0]) {
                 $parametrs['download_limit'] = $other_meta['_download_limit'][0];
             }
             if ($other_meta['_download_expiry'][0]) {
                 $parametrs['download_expiry'] = $other_meta['_download_expiry'][0];
             }
             if ($other_meta['_tax_status'][0]) {
                 $parametrs['tax_status'] = $other_meta['_tax_status'][0];
             }
             if ($other_meta['_tax_class'][0]) {
                 $parametrs['tax_class'] = $other_meta['_tax_class'][0];
             }
             if ($other_meta['_manage_stock'][0] == 'yes') {
                 $parametrs['manage_stock'] = true;
             } else {
                 $parametrs['manage_stock'] = false;
             }
             if ($other_meta['_stock'][0]) {
                 $parametrs['stock_quantity'] = $other_meta['_stock'][0];
             }
             if ($other_meta['_stock_status'][0]) {
                 $parametrs['stock_status'] = $other_meta['_stock_status'][0];
             }
             if ($other_meta['_backorders'][0]) {
                 $parametrs['backorders'] = $other_meta['_backorders'][0];
             }
             if ($other_meta['_virtual'][0] == 'yes') {
                 $parametrs['virtual'] = true;
             } else {
                 $parametrs['virtual'] = false;
             }
             if ($other_meta['_downloadable'][0] == 'yes') {
                 $parametrs['downloadable'] = true;
             } else {
                 $parametrs['downloadable'] = false;
             }
             if ($other_meta['_backorders'][0] == 'yes') {
                 $parametrs['backorders_allowed'] = true;
             } else {
                 $parametrs['backorders_allowed'] = false;
             }
             if ($other_meta['_variation_description'][0]) {
                 $parametrs['description'] = $other_meta['_variation_description'][0];
             }
             if ($other_meta['_sku'][0]) {
                 $parametrs['sku'] = $other_meta['_sku'][0];
             }
             if ($var_product->get_shipping_class()) {
                 $parametrs['shipping_class'] = $var_product->get_shipping_class();
             }				 
             if ($var_product->get_shipping_class_id()) {
                 $parametrs['shipping_class_id'] = woo_sync_gettargetsiteobjid($var_product->get_shipping_class_id() , 'shipping_classes');
             }			 
             if ($other_meta['_weight'][0]) {
                 $parametrs['weight'] = $other_meta['_weight'][0];
             }
             if ($other_meta['_length'][0] != '' or $other_meta['_width'][0] != '' or $other_meta['_height'][0] != '') {
                 $parametrs['dimensions'] = array(
                     'length' => $other_meta['_length'][0],
                     'width' => $other_meta['_width'][0],
                     'height' => $other_meta['_height'][0]
                 );
             }
			  $sorted_atrribute = array();
             foreach ($dd as $key => $value) {
                 if ($key == 'attributes' and $value != '') {
                     $vari_att = $dd['attributes'];
                     foreach ($vari_att as $this_att => $valu) {
                         $list_product_att = (array) $product->get_attributes();
                         $jat              = 0;
                         foreach ($list_product_att as $att_data) {
                             $wcp    = $att_data->get_data();
                             $ss     = 0;
                             $wcp_id = woo_sync_gettargetsiteobjid($wcp['id'] , 'attribute');
                             if ($wcp_id) {
                                 $att_id = $wcp_id;
                             } else {
                                 $att_id = $wcp['id'];
                             }

							 			
                             if ($this_att == 'attribute_' . $wcp['name']) {
                                 if ($wcp['is_taxonomy']) {
                                     $sorted_atrribute[$jat] = array(
                                         'id' => $att_id,
                                         'option' => $valu
                                     );
                                 } else {
                                     $sorted_atrribute[$jat] = array(
                                         'name' => $wcp['name'],
                                         'option' => $valu
                                     );
                                 }
                             }
                             $jat++;
                         }
                     }
                     $parametrs['attributes'] = $sorted_atrribute;
                 }
             }
			 $parametrs['status'] = get_post_status($dd['variation_id']);
             if (in_array($dd['variation_id'], $oldest_variations)) {
                 $action = 'create';
             } else {
                 $action = 'edit';
             }
             if ($action == 'create') {
             $var_id = $dd['variation_id'];
			 
            if (get_post_meta($post->ID, 'woo_sync_create_product_now', true)) {	  
                woo_sync_rest_api_product_variation_handle($parametrs ,$post , $action , $product_id , $var_id);
			}else{
				woo_sync_future_publish_product_variation($parametrs ,$post , $action , wp_get_post_parent_id($dd['variation_id']) , $var_id);
			}			 
			 
             }
			 
             if ($action == 'edit') {
                  $replacer = woo_sync_gettargetsiteobjid($dd['variation_id'] , 'product_variation');
                 if ($replacer) {
                     $var_id = $replacer;
                 } else {
                     $var_id = $dd['variation_id'];
                 }

            if (get_post_meta($post->ID, 'woo_sync_create_product_now', true)) {	  
                woo_sync_rest_api_product_variation_handle($parametrs ,$post , $action , $product_id , $var_id);
			}else{
				woo_sync_future_publish_product_variation($parametrs ,$post , $action , wp_get_post_parent_id($dd['variation_id']) , $var_id);
			}					 
			 
             }
         }
     }
	 }	 
	 
	 
	 
	 

     add_action('wp_trash_post', 'woo_sync_trash_product');
     function woo_sync_trash_product($postid)
     {
     remove_action('save_post_product', 'woo_sync_save_products_changes');		 
         global $post_type;
		 		if (!get_option('woo_sync_creating_started')){
         if (get_post_type($postid) == 'product') {
             $another_id = woo_sync_gettargetsiteobjid($postid , 'product');
             if ($another_id) {
                 $product_id = $another_id;
             }else{
				 $product_id = $postid;
			 }

		 global $product;
         $product  = wc_get_product($postid);			 
		 $post = get_post($postid);	 
			 $parametrs = array(
                              'name' => $product->get_name()
                               );							  
			 

            if (get_post_meta($postid, 'woo_sync_create_product_now', true) or get_option('woo_sync_product_immediately') == 'on') {	  
                woo_sync_rest_api_product_handle($parametrs ,$post , 'trash' , $product_id , 'now');
			}else{
				woo_sync_future_publish_product($parametrs ,$post , 'trash' , $product_id);
			}	
			
         } else {
             return;
         }
     }
	 }
	 

     add_action('untrash_post', 'woo_sync_untrash_product');
     function woo_sync_untrash_product($postid)
     {
     remove_action('save_post_product', 'woo_sync_save_products_changes');
         global $post_type;
		if (!get_option('woo_sync_creating_started')){		 
         if (get_post_type($postid) == 'product') {
             $another_id = woo_sync_gettargetsiteobjid($postid , 'product');
             if ($another_id) {
                 $product_id = $another_id;
             }else{
				 $product_id = $postid;
			 }

		 global $product;
         $product  = wc_get_product($postid);			 
		 $post = get_post($postid);				 
			 $parametrs = array(
                              'name' => $product->get_name(),
							  'status' => get_post_meta($postid, '_wp_trash_meta_status', true),
                               );							  

            if (get_post_meta($postid, 'woo_sync_create_product_now', true) or get_option('woo_sync_product_immediately') == 'on') {	  
                woo_sync_rest_api_product_handle($parametrs ,$post , 'untrash' , $product_id , 'now');
			}else{
				woo_sync_future_publish_product($parametrs ,$post , 'untrash' , $product_id);
			}	

         } else {
             return;
         }
     }	 
	 }
	 
	 

     add_action('before_delete_post', 'woo_sync_delete_product');
     function woo_sync_delete_product($postid)
     {
     remove_action('save_post_product', 'woo_sync_save_products_changes');
         global $post_type;
		if (!get_option('woo_sync_creating_started')){		 
         if (get_post_type($postid) == 'product') {
             $another_id = woo_sync_gettargetsiteobjid($postid , 'product');
             if ($another_id) {
                 $product_id = $another_id;
             }else{
				 $product_id = $postid;
			 }

		 global $product;
         $product  = wc_get_product($postid);			 
		 $post = get_post($postid);				 
			 $parametrs = array(
                              'name' => $product->get_name()
                               );							  

            if (get_post_meta($postid, 'woo_sync_create_product_now', true) or get_option('woo_sync_product_immediately') == 'on') {	  
                woo_sync_rest_api_product_handle($parametrs ,$post , 'delete' , $product_id , 'now');
			}else{
				woo_sync_future_publish_product($parametrs ,$post , 'delete' , $product_id);
			}	

         } else {
             return;
         }
     }
	 }
	 
	


     add_action('delete_post_meta', 'woo_sync_deleted_post_meta', 10, 4);
     function woo_sync_deleted_post_meta($deleted_meta_ids, $post_id, $meta_key, $only_delete_these_meta_values)
     {
      remove_action('save_post_product', 'woo_sync_save_products_changes');  
		 if (!get_option('woo_sync_creating_started')){
         if (get_post_type($post_id) == 'product_variation' and get_post_status($post_id) != 'trash') {
             $replacer      = woo_sync_gettargetsiteobjid(wp_get_post_parent_id($post_id) , 'product');
             if ($replacer) {
                 $product_id = $replacer;
             } else {
                 $product_id = wp_get_post_parent_id($post_id);
             }
             $replacer2 = woo_sync_gettargetsiteobjid($post_id , 'product_variation');
             if ($replacer2) {
                 $var_id = $replacer2;
             } else {
                 $var_id = $post_id;
             }
			$post = get_post($post_id);
			 if (get_option('woo_sync_deleted_var'.$post_id) == ''){
			    update_option('woo_sync_deleted_var'.$post_id , true);		 
            if (get_post_meta(wp_get_post_parent_id($post_id) , 'woo_sync_create_product_now', true) or get_option('woo_sync_product_immediately') == 'on') {	 	  
                woo_sync_rest_api_product_variation_handle('' , $post , 'delete' , $product_id , $var_id);
			}else{
				woo_sync_future_publish_product_variation('' , $post , 'delete' , wp_get_post_parent_id($post_id) , $var_id);
			}				 
			 }
         }

     }
	 }


     add_action('woocommerce_new_product_variation', 'woo_sync_woocommerce_new_product_variation', 10, 1);	 
     function woo_sync_woocommerce_new_product_variation($id)
     {
         $father  = wp_get_post_parent_id($id);
         $last_id = get_post_meta($father, "woo_sync_variations_ids");
         if ($last_id) {
             array_push($last_id[0], $id);
             $ids = $last_id[0];
         } else {
             $ids = array(
                 $id
             );
         }
         update_post_meta($father, "woo_sync_variations_ids", $ids);
     }




     add_action('save_post', 'woo_sync_save_products_changes', 1000 , 2);	 
     function woo_sync_save_products_changes($post_id, $post)
     {

       //die(json_encode($_POST));
         if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
             return;
         }
         if (!current_user_can('edit_post', $post_id)) {
             return;
         }

         if (!isset($_POST['woo_sync_nonce']) || !wp_verify_nonce($_POST['woo_sync_nonce'] , 'woo_sync_nonce_' . $post_id)) {
             return;
         }
		 
		 
       if (get_post_type($post) === 'product'	|| get_post_type($post) === 'product_variation'){	
 		 $posted_publish_now = sanitize_text_field($_POST['woo_sync_create_product_now']);
         if (isset($posted_publish_now)) {
			 $is_local_host = get_option("woo_sync_is_local_host");
			 if ($is_local_host){
			 $up_res = upload_product_images_before_publish();
			 }else{
			 $up_res = 'Down';	 
			 }
			 if ($up_res === 'Down'){
             update_post_meta($post_id, 'woo_sync_create_product_now', $posted_publish_now);
			 }else{
					  die(__('woo sync can not connect to your server , Connection failed , please check ftp settings!', 'woocommerce-synchronizer'));
             }					  
         } else {	
             delete_post_meta($post_id, 'woo_sync_create_product_now');
         }
		 
		 if (isset($_POST['type'])){
         $abe_posted = sanitize_text_field($_POST['type']);
		 }
		 
		if (!isset($abe_posted) and get_post_type($post) !== 'product_variation'){	
        $woo_sync_first = get_post_meta($post_id, 'woo_sync_first_publish', true);
        $has_id = woo_sync_gettargetsiteobjid($post->ID , 'product');		 
        $status         = get_post_status($post->ID); 
		if ($status != 'future' and $status != 'auto-draft' and $status != 'trash') {
                 if ($woo_sync_first != true and $has_id == '') {
                     update_post_meta($post_id, 'woo_sync_tar_product_created', true);					 
                     woo_sync_create_new_product($post, "create");
                 } else { 
                     woo_sync_create_new_product($post, "edit");
                 }
            }
		}
		
		
		////////////if user set a time for product publish (future publish)
		if (get_post_status($post->ID) === 'future'){
			 $is_local_host = get_option("woo_sync_is_local_host");
			 if ($is_local_host){			
			 $up_res = upload_product_images_before_publish();
			 }else{
			 $up_res = 'Down';	 
			 }
			 if ($up_res === 'Down'){
             update_post_meta($post_id, 'woo_sync_create_product_now', true);
			 }else{
					  die(__('woo sync can not connect to your server , Connection failed , please check ftp settings!', 'woocommerce-synchronizer'));
             }			 
		}


	
        }	
		
     }


	 
	 function upload_product_images_before_publish(){
       //die(json_encode($_POST));
	   $posted_thum = sanitize_text_field($_POST['_thumbnail_id']);
	   $posted_gal = sanitize_text_field($_POST['product_image_gallery']);
	   $posted_uii = sanitize_text_field($_POST['upload_image_id']);
	   if ($posted_thum != '-1' || $posted_gal != '' || !empty($posted_uii)){
       $thumnail_id = $posted_thum;
       $gallery  = $posted_gal;
	   $varian = '';
	   $vars = '';
	   if (!empty($posted_uii)){
       $varian  = $posted_uii;
	   $vars = implode(',' , $varian);
	   }	   
       $images = $thumnail_id.','.$gallery.','.$vars;
       $search = array_filter(explode(',' , $images));	
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
        foreach($search as $file){
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
		}         
                  ftp_close($connection);
                  return $result;
	   }else{
        return 'Down';
	   }		
	 
	 }

	 
	 
     add_action('transition_post_status', 'woo_sync_future_post', 10, 3);	 
     function woo_sync_future_post($new, $old, $post)
     {
         if ($post->post_type == 'product' && $new == 'publish' && $old == 'future') {
             update_post_meta($post->ID, 'woo_sync_create_product_now', true);
             $woo_sync_first = get_post_meta($post->ID, 'woo_sync_first_publish', true);
             if ($woo_sync_first != true) {
                 woo_sync_create_new_product($post, "create");
             } else {
                 woo_sync_create_new_product($post, "edit");
                 }
         }
     }



     add_filter('is_protected_meta', 'woo_sync_is_protected_meta', 10, 3);	 
     function woo_sync_is_protected_meta($protected, $meta_key, $meta_type)
     {
         switch ($meta_key) {
             case 'woo_sync_first_publish':
                 $protected = true;
                 break;
             case 'woo_sync_create_product_now_result':
                 $protected = true;
                 break;
             case 'woo_sync_create_product_now_result_error':
                 $protected = true;
                 break;
             case 'woo_sync_another_site_pid':
                 $protected = true;
                 break;
             case 'woo_sync_create_product_now':
                 $protected = true;
                 break;
             case 'woo_sync_Wordpress_error':
                 $protected = true;
                 break;
             case 'woo_sync_another_site_var_id':
                 $protected = true;
                 break;
             case 'woo_sync_tar_product_created':
                 $protected = true;
                 break;				 
         }
         return $protected;
     }	 
	 	 
?>