<?php
     /*
     @create attributes
     @update attributes
     @delete attributes
     */
	 
     add_action('woocommerce_attribute_added', 'woo_sync_after_attribute_added');
     function woo_sync_after_attribute_added($term_id)
     {
		 if (!get_option('woo_sync_creating_started')){		 
         $attr         = wc_get_attribute($term_id);
		 $parametrs = array(
                 'name' => $attr->name,
                 'slug' => $attr->slug,
                 'type' => $attr->type,
                 'order_by' => $attr->order_by,
                 'has_archives' => $attr->has_archives
             );
			 
         if (get_option('woo_sync_att_immediately') == 'on'){
	   	woo_sync_rest_api_attributes_handle($parametrs , $term_id , 'create');	 
		 }else{
		 woo_sync_future_publish_attributes( $parametrs , $term_id , 'create');	 
		 }
		 }
     }
	 
 
     add_action('woocommerce_attribute_updated', 'woo_sync_after_attribute_updated');
     function woo_sync_after_attribute_updated($term_id)
     {
		if (!get_option('woo_sync_creating_started')){		 
         $attr   = wc_get_attribute($term_id);
		 $parametrs = array(
                 'name' => $attr->name,
                 'slug' => $attr->slug,
                 'type' => $attr->type,
                 'order_by' => $attr->order_by,
                 'has_archives' => $attr->has_archives
             );
		
         if (get_option('woo_sync_att_immediately') == 'on'){
         $has_id = woo_sync_gettargetsiteobjid($term_id , 'attribute');
         if ($has_id) {
             $term_id = $has_id;
         }			 	 
	   	woo_sync_rest_api_attributes_handle($parametrs , $term_id , 'edit');	 
		 }else{
		 woo_sync_future_publish_attributes( $parametrs , $term_id , 'edit');	 
		 }		

     }
	 }
	 

 
     add_action('woocommerce_attribute_deleted', 'woo_sync_delete_attribute' ,  10 ,  3 );
     function woo_sync_delete_attribute($term_id , $name , $taxname)
     {
		if (!get_option('woo_sync_creating_started')){		 
		 $parametrs = array(
                 'name' => $taxname
             );		 

         if (get_option('woo_sync_att_immediately') == 'on'){
         $has_id = woo_sync_gettargetsiteobjid($term_id , 'attribute');
         if ($has_id) {
             $term_id = $has_id;
         }					 
	   	woo_sync_rest_api_attributes_handle($parametrs , $term_id , 'delete');	 
		 }else{
		 woo_sync_future_publish_attributes( $parametrs , $term_id , 'delete');	 
		 }		
		}
     }

	 
?>