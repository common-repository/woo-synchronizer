<?php
     /*
     @create attributes terms and product tag
     @update attributes terms and product tag
     @delete attributes terms and product tag
     */
	 
     

     add_action('create_term', 'woo_sync_create_attribute_term', 10, 3);
     function woo_sync_create_attribute_term($term, $tt_id, $tax)
     {
		if (!get_option('woo_sync_creating_started')){
         $termi = get_term($term, $tax);
         if ($tax == 'product_tag') {
			 $parametrs = array(
                     'name' => $termi->name,
                     'slug' => $termi->slug,
                     'description' => $termi->description
                 );

		 if (get_option('woo_sync_tag_immediately') == 'on'){
         woo_sync_rest_api_tags_handle( $parametrs , $term , 'create');
		 }else{
		  woo_sync_future_publish_tags($parametrs , $term , 'create');	 
		 }	 
				 
         } else {
             $productdetect = explode('pa_', $tax);
             if ($productdetect[0] == '' and $productdetect[1] != '') {
                 $attributesid = wc_attribute_taxonomy_id_by_name($productdetect[1]);
                 $has_id       = woo_sync_gettargetsiteobjid($attributesid , 'attribute');
                 if ($has_id) {
                     $attributesid = $has_id;
                 }
				 
				 $parametrs = array(
                         'name' => $termi->name,
                         'slug' => $termi->slug,
                         'description' => $termi->description
                     );

		 if (get_option('woo_sync_att_immediately') == 'on'){
         woo_sync_rest_api_att_term_handle( $parametrs , $term , 'create' , $attributesid , $tax);
		 }else{
		  woo_sync_future_publish_att_term($parametrs , $term , 'create' , $attributesid , $tax);	 
		 }						 

             }
         }
     }
	 }
	 
	 

     add_action('edit_terms', 'woo_sync_before_edit_attribute_term', 10, 2);
     function woo_sync_before_edit_attribute_term($term_id, $tax)
     {
         clean_term_cache($term_id , $tax);
     }
	 
	 
	

      add_action( 'pre_term_description', 'woo_sync_before_edit_descriptions', 10, 2 ); 

      function woo_sync_before_edit_descriptions( $description, $taxonomy ) {
       switch ( $taxonomy ) {
         case 'product_tag':
            update_option("woo_sync_tag_description" , $description);
            break;
       }
	   
	   if (strpos( $taxonomy , 'pa_') !== false) {
		$termdes = explode('pa_' , $taxonomy);
        if ($termdes[1]){
            update_option("woo_sync_term_description" , $description);
		}		
	   }
	   
       return $description;  
       }	
	 

 
     add_action('edited_terms', 'woo_sync_after_edit_attribute_term', 10, 2);
     function woo_sync_after_edit_attribute_term($term_id, $tax)
     {
		if (!get_option('woo_sync_creating_started')){		 
         $termi = get_term($term_id, $tax);
         if ($tax == 'product_tag') {
             $has_id = woo_sync_gettargetsiteobjid($term_id , 'tag');
             if ($has_id) {
                 $term_id = $has_id;
             }
			 $parametrs = array(
                     'name' => $termi->name,
                     'slug' => $termi->slug,
                     'description' => get_option("woo_sync_tag_description")
                 );
				 
		 if (get_option('woo_sync_tag_immediately') == 'on'){
         woo_sync_rest_api_tags_handle( $parametrs , $term_id , 'edit');
		 }else{
		  woo_sync_future_publish_tags($parametrs , $term_id , 'edit');	 
		 }					 

         } else {
             $productdetect = explode('pa_', $tax);
             if ($productdetect[0] == '' and $productdetect[1] != '') {
                 $attributesid = wc_attribute_taxonomy_id_by_name($productdetect[1]);
                 $has_id       = woo_sync_gettargetsiteobjid($term_id , 'att_terms');
                 if ($has_id) {
                     $term_id = $has_id;
                 }
                 $has_att_id = woo_sync_gettargetsiteobjid($attributesid , 'attribute');
                 if ($has_att_id) {
                     $attributesid = $has_att_id;
                 }
				 
				 $parametrs = array(
                         'name' => $termi->name,
                         'slug' => $termi->slug,
                         'description' => get_option("woo_sync_term_description")
                     );
					 
		 if (get_option('woo_sync_att_immediately') == 'on'){
         woo_sync_rest_api_att_term_handle( $parametrs , $term_id , 'edit' , $attributesid , $tax);
		 }else{
		  woo_sync_future_publish_att_term($parametrs , $term_id , 'edit' , $attributesid , $tax);	 
		 }						 

             }
         }
     }
	 } 
	 



     add_action('pre_delete_term', 'woo_sync_delete_attribute_term', 10, 2);
     function woo_sync_delete_attribute_term($term, $tax)
     {
		 
		if (!get_option('woo_sync_creating_started')){		 
         $termi = get_term($term, $tax);
         if ($tax == 'product_tag') {
             $has_id = woo_sync_gettargetsiteobjid($term , 'tag');
             if ($has_id) {
                 $term = $has_id;
             }

			 $parametrs = array(
                     'name' => $termi->name,
                     'slug' => $termi->slug,
                     'description' => $termi->description
                 );			 

		 if (get_option('woo_sync_tag_immediately') == 'on'){
         woo_sync_rest_api_tags_handle( $parametrs , $term , 'delete');
		 }else{
		  woo_sync_future_publish_tags($parametrs , $term , 'delete');	 
		 }	
		 
			 
         } else {
             $productdetect = explode('pa_', $tax);
             if ($productdetect[0] == '' and $productdetect[1] != '') {
                 $attributesid = wc_attribute_taxonomy_id_by_name($productdetect[1]);
                 $has_id       = woo_sync_gettargetsiteobjid($term , 'att_terms');
                 if ($has_id) {
                     $term = $has_id;
                 }
                 $has_att_id = woo_sync_gettargetsiteobjid($attributesid , 'attribute');
                 if ($has_att_id) {
                     $attributesid = $has_att_id;
                 }

			 $parametrs = array(
                     'name' => $termi->name,
                     'slug' => $termi->slug,
                     'description' => $termi->description
                 );					 
				 
		 if (get_option('woo_sync_att_immediately') == 'on'){
         woo_sync_rest_api_att_term_handle( $parametrs , $term , 'delete' , $attributesid , $tax);
		 }else{
		  woo_sync_future_publish_att_term($parametrs , $term , 'delete' , $attributesid , $tax);	 
		 }					 

             }
         }
     }
	 } 
	
?>