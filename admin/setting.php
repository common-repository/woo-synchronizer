<?php

///////////////////////////////////////// admin setting page

function woo_sync_setting_page(){

?>
<?php header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); header('Cache-Control: no-store, no-cache, must-revalidate'); header('Cache-Control: post-check=0, pre-check=0', FALSE); header('Pragma: no-cache'); ?>
<div class="woo_sync_logo_container"><img id="woo_sync_logo" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/logo.png';?>"/></div><hr>
<div class = "woo_sync_body">

       <form action="options.php" method="post"> 
        
		<?php
        	settings_fields('woo-sync-settings');
	        do_settings_sections('woo-sync-settings');
         ?> 

 <div id="tabs">
 
 <?php
 if (get_option('woo_sync_target_url')){
?>	 
  <ul>
    <li><div class="dashicons dashicons-upload"></div><a href="#tabs-2"><?php echo __('Publish List', 'woocommerce-synchronizer');?></a></li>	
    <li><div class="dashicons dashicons-download"></div><a href="#tabs-3"><?php echo __('Download Products', 'woocommerce-synchronizer');?></a></li>
    <li><div class="dashicons dashicons-admin-generic"></div><a href="#tabs-1"><?php echo __('Settings', 'woocommerce-synchronizer');?></a></li>	
  </ul>	
<?php	 
 }else{
?>	 
  <ul>
    <li><div class="dashicons dashicons-admin-generic"></div><a href="#tabs-1"><?php echo __('Settings', 'woocommerce-synchronizer');?></a></li>
    <li><div class="dashicons dashicons-upload"></div><a href="#tabs-2"><?php echo __('Publish List', 'woocommerce-synchronizer');?></a></li>	
    <li><div class="dashicons dashicons-download"></div><a href="#tabs-3"><?php echo __('Download Products', 'woocommerce-synchronizer');?></a></li>
  </ul>	
<?php	
 }
 ?>

  
  
  <div id="tabs-1">

 <table class="form-table">
 
     <tr>
      <th > <?php echo __("Target Store url", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_target_url" name="woo_sync_target_url" type="text" value="<?php echo esc_attr(get_option('woo_sync_target_url')); ?>" /><br>
	   <p class="woo_sync_example" > ex : https://example.com </p>
	   </td>	  
	</tr>	
	
	
	 <tr>
       <th > <?php echo __("Target Store Consumer key", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_customer_key" name="woo_sync_customer_key" type="text" value="<?php echo esc_attr(get_option('woo_sync_customer_key')); ?>"  /><br>
	   	   <p class="woo_sync_example" > ex : ck_c810d0414cc0113f10c3690791e7e515b6e25051 </p>
	   </td>	  
	</tr>
	
	 <tr>
       <th > <?php echo __("Target Store Consumer secret", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_secret_key" name="woo_sync_secret_key" type="text" value="<?php echo esc_attr(get_option('woo_sync_secret_key')); ?>"  /><br>
	   	   <p class="woo_sync_example" > ex : cs_85d5c849e82451ba2a27e1f8b1455e95d1a5617e </p>
		   
		   	   <?php add_thickbox(); ?>
               <a href="#TB_inline?width=auto&height=auto&inlineId=modal-window-id" id="woo_sync_video1_loader_button" class="thickbox"><?php echo __("View the tutorial of how to create consumer key and secret", 'woocommerce-synchronizer');?></a>
                <div id="modal-window-id" style="display:none;">
                  <video width="auto" controls>
                    <source src="<?php echo WOO_SYNC_FILE_URL.'assets/mp4/learning.mp4';?>" type="video/mp4">
                  </video>
                </div>
		   
	   </td>	  
	</tr>
	

	 <tr>
       <th > <?php echo __("Test Rest Api Connection", 'woocommerce-synchronizer'); ?> </th> 
       <td> 	   
	<button type="button" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="test_connection_restapi" ><?php echo __('Click Here', 'woocommerce-synchronizer'); ?></button>
	   	   <div class="woo_sync_load_con" style="text-align:center;">
		   <img id="woo_sync_loader1" class="woo_sync_loader" style="height: 40px; display:none;" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.png';?>" srcset="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.svg';?>" alt="woo_sync_loader">
		   </div>
		   
		   <div id="woo_sync_test_restapi_result" ></div>
	   </td>	  
	</tr>	

	
	 <tr>
       <th > <?php echo __("*woo sync target* access code", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_target_access_code" name="woo_sync_target_access_code" type="text" value="<?php echo esc_attr(get_option('woo_sync_target_access_code')); ?>"  /><br>
	        <a href="https://woo.ttmga.com/woo-sync/woo_sync_target.zip"><?php echo __("Download *woo sync target*", 'woocommerce-synchronizer'); ?></a><br><?php echo __("This plugin should be installed on the target store. Please read the product guide.", 'woocommerce-synchronizer'); ?> 
            <p><?php echo __("If you have installed the *woo sync target* plugin on the target site, after activation , an access code will be announced to you. Insert that code in the top field.", 'woocommerce-synchronizer'); ?></p> 
	   </td>	  
	</tr>

	
   <tr>
   <th><?php echo __("immediate change options :", 'woocommerce-synchronizer'); ?></th>
   <td>
    <hr>
   <td>	
	</tr>	
	
	 <tr>
       <th ><?php echo __('Products', 'woocommerce-synchronizer');?> </th>
       <td> 
       <input type="checkbox" name="woo_sync_product_immediately" <?php echo esc_attr(get_option('woo_sync_product_immediately')) == 'on' ? 'checked="checked"' : '';?> /><?php echo __('Enable', 'woocommerce-synchronizer');?><br>
            <p><?php echo __("If this option is enabled, whenever you publish a new product or delete or edit products, these changes will be applied immediately to the target store.", 'woocommerce-synchronizer'); ?></p> 
       </td>
	</tr>	
	
	
	 <tr>
       <th ><?php echo __('Categories', 'woocommerce-synchronizer');?> </th>
       <td> 
       <input type="checkbox" name="woo_sync_cat_immediately" <?php echo esc_attr(get_option('woo_sync_cat_immediately')) == 'on' ? 'checked="checked"' : '';?> /><?php echo __('Enable', 'woocommerce-synchronizer');?><br>
            <p><?php echo __("If this option is enabled, any add/edit or delete applied to the product category section will be applied immediately to the target store.", 'woocommerce-synchronizer'); ?></p> 
       </td>
	</tr>	

	 <tr>
       <th ><?php echo __('Tags', 'woocommerce-synchronizer');?> </th>
       <td> 
       <input type="checkbox" name="woo_sync_tag_immediately" <?php echo esc_attr(get_option('woo_sync_tag_immediately')) == 'on' ? 'checked="checked"' : '';?> /><?php echo __('Enable', 'woocommerce-synchronizer');?><br>
            <p><?php echo __("If this option is enabled, all changes to the tags section are immediately applied to the tags section of the target store.", 'woocommerce-synchronizer'); ?></p> 
       </td>
	</tr>	


	 <tr>
       <th ><?php echo __('Attributes', 'woocommerce-synchronizer');?> </th>
       <td> 
       <input type="checkbox" name="woo_sync_att_immediately" <?php echo esc_attr(get_option('woo_sync_att_immediately')) == 'on' ? 'checked="checked"' : '';?> /><?php echo __('Enable', 'woocommerce-synchronizer');?><br>
            <p><?php echo __("If this option is enabled, all changes to the product features section are immediately applied to the product features of the target store.", 'woocommerce-synchronizer'); ?></p> 
       </td>
	</tr>	


	 <tr>
       <th ><?php echo __('Do you use the plugin on the localhost?', 'woocommerce-synchronizer');?> </th>
       <td> 
       <input type="checkbox" id="is_on_localhost" name="woo_sync_is_local_host" <?php echo esc_attr(get_option('woo_sync_is_local_host')) == 'on' ? 'checked="checked"' : '';?> /><?php echo __('Yes', 'woocommerce-synchronizer');?><br>
       </td>
	</tr>	
              

     <tr class="woo_sync_in_local_host">
      <th > <?php echo __("Further Details", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <p><?php echo __("This plugin uses the Woocommerce Rest Api and capability to connect to the target site. Since there are always limitations in this connection, when you want to connect with your online store by using localhost and when creating products, if you choose a picture for your product, your online store cannot download the picture from your localhost and upload it to the server. Thus, creating a product will face an error and no product will be created in your store. To fix this problem, all files and pictures uploaded to your localhosts should be uploaded to an online server, which can be performed using the following settings.", 'woocommerce-synchronizer'); ?></p>
	   
	   		    <a href="https://www.interserver.net/tips/kb/create-and-manage-ftp-accounts-in-cpanel/" target="_blank"><?php echo __("How to Create an FTP account in Cpanel", 'woocommerce-synchronizer');?></a><br>
			 <a href="https://www.interserver.net/tips/kb/ftp-management-in-directadmin/" target="_blank"><?php echo __("How to Create an FTP account in DirectAdmin", 'woocommerce-synchronizer');?></a>

	   </td>	  
	</tr>	
			  

     <tr id="woo_sync_server_ip_tr" style="display:none !important;">
      <th > <?php echo __("Target store server IP", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_server_ip" name="woo_sync_server_ip" type="text" value="<?php echo esc_attr(get_option('woo_sync_server_ip')); ?>" /><br>
	   <p class="woo_sync_example" > ex : 170.25.93.74 </p>
	   </td>	  
	</tr>	
	
	
	 <tr class="woo_sync_in_local_host">
       <th > <?php echo __("FTP port", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_server_port" name="woo_sync_server_port" type="text" value="<?php echo esc_attr(get_option('woo_sync_server_port')); ?>"  /><br>
	   	   <p class="woo_sync_example" > default : 21 </p>
	   </td>	  
	</tr>
	
	 <tr class="woo_sync_in_local_host">
       <th > <?php echo __("FTP Username", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_ftp_user" name="woo_sync_ftp_user" type="text" value="<?php echo esc_attr(get_option('woo_sync_ftp_user')); ?>"  /><br>
	   	   <p class="woo_sync_example" > ex : JohnDoe </p>
	   </td>	  
	</tr>
	
	
     <tr class="woo_sync_in_local_host">
      <th > <?php echo __("FTP Password", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" id="woo_sync_ftp_pass" name="woo_sync_ftp_pass" type="text" value="<?php echo esc_attr(get_option('woo_sync_ftp_pass')); ?>" /><br>
	   <p class="woo_sync_example" > ex : ewcnj545DDC </p>
	   </td>	  
	</tr>	
	
	
	 <tr id="woo_sync_server_url" style="display:none !important;">
       <th > <?php echo __("Upload url", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" name="woo_sync_url_cdn" id="woo_sync_url_cdn" type="text" value="<?php echo esc_attr(get_option('woo_sync_url_cdn')); ?>"  /><br>
	   	   <p class="woo_sync_example" > ex : https://example.com/wp-content/uploads/</p>
	   </td>	  
	</tr>
	
	 <tr  id="woo_sync_server_path_tr" style="display:none !important;">
       <th > <?php echo __("Upload path", 'woocommerce-synchronizer'); ?> </th> 
       <td> 
       <input class="woo_sync_input" name="woo_sync_server_path" id="woo_sync_server_path" type="text" value="<?php echo esc_attr(get_option('woo_sync_server_path')); ?>"  /><br>
	   	   <p class="woo_sync_example" > ex : /domains/example.com/public_html/wp-content/uploads/ </p>
		   

	   </td>	  
	</tr>
	
	
	 <tr class="woo_sync_in_local_host">
       <th > <?php echo __("Test FTP Connection", 'woocommerce-synchronizer'); ?> </th> 
       <td> 	   
	<button type="button" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="test_connection_ftp" ><?php echo __('Click Here', 'woocommerce-synchronizer'); ?></button>
	   	   <div class="woo_sync_load_con" style="text-align:center;">
		   <img id="woo_sync_loader2" class="woo_sync_loader" style="height: 40px; display:none;" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.png';?>" srcset="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.svg';?>" alt="woo_sync_loader">
		   </div>
		   
		   <div id="woo_sync_test_ftp_result" ></div>
	   </td>	  
	</tr>		
 
	
 </table>	
<br><br><br>

<?php submit_button(); ?> 
           
      </form>  
 
  </div>
  
  
  <div id="tabs-2">

  <?php 
  if (get_option('woo_sync_target_access_code')){			
?>	 
  
  <h3 style="font-family: Tanha;color: #efab1f;"><?php echo __("Update before Publish", 'woocommerce-synchronizer'); ?></h3>
 

	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_update_changes_target" ><div class="dashicons dashicons-update"></div><?php echo __('update latest changes', 'woocommerce-synchronizer'); ?></button>
	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_delete_changes_log" ><div class="dashicons dashicons-trash"></div><?php echo __('Clear latest changes log', 'woocommerce-synchronizer'); ?></button>	
	<br><br>
		   <img id="woo_sync_loader32" class="woo_sync_loader" style="height: 40px; display:none;" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.png';?>" srcset="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.svg';?>" alt="woo_sync_loader">

		   <div id="woo_sync_publish_future_result2" ><code id="woo_sync_response_updater" style="display : none; direction: ltr;"><?php echo __("Updating", 'woocommerce-synchronizer'); ?><br></code></div>
		   
 <hr> 
  <br>
  <br>		   
<?php
}
?>
  

  <div class="table">
    
    <div class="row header">
      <div class="cell">
        <?php echo __("ID", 'woocommerce-synchronizer'); ?>
      </div>	
      <div class="cell">
        <?php echo __("Name", 'woocommerce-synchronizer'); ?>
      </div>
      <div class="cell">
        <?php echo __("Type", 'woocommerce-synchronizer'); ?>
      </div>
      <div class="cell">
        <?php echo __("Action", 'woocommerce-synchronizer'); ?>
      </div>
      <div class="cell">
        <?php echo __("Date", 'woocommerce-synchronizer'); ?>
      </div>	  
      <div class="cell">
        <?php echo __("Cancel", 'woocommerce-synchronizer'); ?>
      </div>
    </div>
    
<?php
        global $wpdb;
		$row = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_future_publish  ORDER BY id DESC");
		$i = 0;
		foreach($row as $row)
			{	
			
			
 switch ($row->object_type) {
    case "product":
       $type = __("Product", 'woocommerce-synchronizer');
        break;
    case "attribute":
         $type = __("Attribute", 'woocommerce-synchronizer');
        break;
    case "category":
         $type = __("Category", 'woocommerce-synchronizer');
        break;
    case "attribute_term":
         $type = __("Attribute Term", 'woocommerce-synchronizer');
        break;
    case "tag":
         $type = __("Tag", 'woocommerce-synchronizer');
        break;	
    case "file":
         $type = __("Media", 'woocommerce-synchronizer');
        break;			
}


 switch ($row->action) {
    case "create":
       $action = __("Create", 'woocommerce-synchronizer');
        break;
    case "edit":
         $action = __("Edit", 'woocommerce-synchronizer');
        break;
    case "delete":
         $action = __("Delete", 'woocommerce-synchronizer');
        break;
    case "trash":
         $action = __("Trash", 'woocommerce-synchronizer');
        break;
    case "untrash":
         $action = __("Untrash", 'woocommerce-synchronizer');
        break;		
}
			
			if ($row->object_type != 'product_variation'){
				$i++;
?>			
	
    <div class="row" id="woo-row<?php echo $row->id ;?>">
      <div class="cell" data-title="<?php echo __("ID", 'woocommerce-synchronizer'); ?>">
        <?php echo $row->object_id ;?>
      </div>	
      <div class="cell" data-title="<?php echo __("Name", 'woocommerce-synchronizer'); ?>">
        <?php echo $row->object_name ;?>
      </div>
      <div class="cell" data-title="<?php echo __("Type", 'woocommerce-synchronizer'); ?>">
        <?php echo $type;?>
      </div>
      <div class="cell" data-title="<?php echo __("Action", 'woocommerce-synchronizer'); ?>">
        <?php echo $action;?>
      </div>
      <div class="cell" data-title="<?php echo __("Date", 'woocommerce-synchronizer'); ?>">
        <?php echo $row->date ;?>
      </div>	  
      <div class="cell" data-title="<?php echo __("Cancel", 'woocommerce-synchronizer'); ?>">
        <div class="woo-sync-dismiss dashicons dashicons-dismiss" data-ajax="<?php echo admin_url('admin-ajax.php');?>" data-disid="<?php echo $row->id ;?>" data-kind="<?php echo $row->object_type;?>" data-objid="<?php echo $row->object_id;?>"></div>
      </div>
    </div>
    
<?php   
			}
			}
			

?>			
  </div>
<?php

     if ($i == 0){
    echo '<div class="woo_sync_nothing_found">'.__('There is no item to publish!', 'woocommerce-synchronizer').'</div>';
	 }	 

  
if ($i != 0){ ?> 
	<button type="button" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_publish_future" ><?php echo __('Publish all items', 'woocommerce-synchronizer'); ?></button>
	   	   <div class="woo_sync_load_con" style="text-align:center;">
		   <img id="woo_sync_loader3" class="woo_sync_loader" style="height: 40px; display:none;" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.png';?>" srcset="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.svg';?>" alt="woo_sync_loader">
		   </div>
		   
		   <div id="woo_sync_publish_future_result" ><code id="woo_sync_response_publish" style="display : none;"><?php echo __("Publishing...", 'woocommerce-synchronizer'); ?><br></code></div>  
 <?php }?>
  </div>

  <div id="tabs-3">
        <?php
		 $rows = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list WHERE status != 'Down' ORDER BY id ASC");
		$counter = 0;
		foreach($rows as $row)
			{	
			$counter++;
			}
          ?>
		  
		  
		  
  <div class="tab3content">

<?php
if ($counter != 0){
?>
	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_continue_download_objects" ><div class="dashicons dashicons-backup"></div><?php echo __('Continue', 'woocommerce-synchronizer'); ?></button>
<?php	
}

		 $rows1 = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}woo_sync_download_list ORDER BY id ASC");
		$counter1 = 0;
		foreach($rows1 as $row1)
			{	
			$counter1++;
			}	

if ($counter1 == 0){			
?>	 
	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_download_objects" ><div class="dashicons dashicons-download"></div><?php echo __("Download Products of target store", 'woocommerce-synchronizer'); ?></button>
<?php
}else{
?>
	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_download_objects" ><div class="dashicons dashicons-update"></div><?php echo __('Update', 'woocommerce-synchronizer'); ?></button>
	<button type="button" data-result="<?php echo WOO_SYNC_FILE_URL.'includes/synchronizer/local/results.txt'; ?>" data-ajax="<?php echo admin_url('admin-ajax.php');?>" id="woo_sync_Delete_objects" ><div class="dashicons dashicons-trash"></div><?php echo __('Clear download history', 'woocommerce-synchronizer'); ?></button>	
<?php } ?>
	
	   	   <div class="woo_sync_load_con" style="text-align:center;">
		   <img id="woo_sync_loader4" class="woo_sync_loader" style="height: 40px; display:none;" src="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.png';?>" srcset="<?php echo WOO_SYNC_FILE_URL.'assets/img/loader.svg';?>" alt="woo_sync_loader">
		   </div>
	</div>	   
		   <div id="woo_sync_download_result" style="display:inline-flex;width: 100%;"><code id="woo_sync_response_download" style="display : none;"><?php echo __("Downloading...", 'woocommerce-synchronizer'); ?><br></code><code id="woo_sync_live_res_terminal" style="display:none;    width: 50%;"></code></div>  
  </div>  
  
</div>
<div class="woo-sync-footer">
<b style="font-weight: 100; " class="woo-sync-copyright-name"> Developer : S.J.Hosseini</b> &nbsp;|&nbsp;
<b style="font-weight: 100;" class="woo-sync-copyright-telegram"> <a href="https://www.codester.com/items/11458/woocommerce-store-management-by-localhost?ref=sjafarhosseini007">Upgrade to premium version</a><b> &nbsp;|&nbsp;
</div> 
</div>

  <script>
 var hash = window.location.hash;
var chosenmethod = hash.substring(1); 
  jQuery( function() {
    jQuery( "#tabs" ).tabs({
  active: chosenmethod
});
  } );
  
  </script>
  
<?php	
}

?>