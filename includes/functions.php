<?php
//////////////////////////////////////////////////////////////////////////////////////
          /*
          @custom functions
          */
//////////////////////////////////////////////////////////////////////////////////////


		  
		  /*
		  @json Auth copied from https://github.com/WP-API/Basic-Auth
		  */
          add_filter('determine_current_user', 'woo_sync_json_basic_auth_handler', 20);
          function woo_sync_json_basic_auth_handler($user)
              {
              global $wp_json_basic_auth_error;
              $wp_json_basic_auth_error = null;
              // Don't authenticate twice
              if (!empty($user))
                  {
                  return $user;
                  }
              // Check that we're trying to authenticate
              if (!isset($_SERVER['PHP_AUTH_USER']))
                  {
                  return $user;
                  }
              $username = $_SERVER['PHP_AUTH_USER'];
              $password = $_SERVER['PHP_AUTH_PW'];
              remove_filter('determine_current_user', 'woo_sync_json_basic_auth_handler', 20);
              $user = wp_authenticate($username, $password);
              add_filter('determine_current_user', 'woo_sync_json_basic_auth_handler', 20);
              if (is_wp_error($user))
                  {
                  $wp_json_basic_auth_error = $user;
                  return null;
                  }
              $wp_json_basic_auth_error = true;
              return $user->ID;
              }
          
          
          
          
          add_filter('rest_authentication_errors', 'woo_sync_json_basic_auth_error');
          function woo_sync_json_basic_auth_error($error)
              {
              if (!empty($error))
                  {
                  return $error;
                  }
              global $wp_json_basic_auth_error;
              return $wp_json_basic_auth_error;
              }		  
          ///////////////////////////////////////////////////



          /*
          @load woo sync text domain for translate purpose
          */		  
		  
          add_action('plugins_loaded', 'woo_sync_load_plugin_textdomain');
          function woo_sync_load_plugin_textdomain()
              {
              load_plugin_textdomain('woocommerce-synchronizer', FALSE, basename(dirname(__FILE__)) . '/languages/');
              }
          
		  
		  
		  

          


          /*
          @add admin menu
          */ 
          add_action('admin_menu', 'woo_sync_admin_menu');
          function woo_sync_admin_menu()
              {
              
              $page_title = 'Woocommerce Synchronizer';
              $menu_title = 'Woo Synchronizer';
              $capability = 'edit_posts';
              $menu_slug  = 'woo_sync_dash';
              $function   = 'woo_sync_setting_page';
              $icon_url   = 'dashicons-woo-sync';
              $position   = 57;
              
              add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
              
              
              }


          /*
          @add woo sync meta box to product page
          */ 
			  
             add_action('add_meta_boxes', 'woo_sync_product_page_meta_box');         
             function woo_sync_product_page_meta_box()
             {
             add_meta_box('woo_sync_publisher_control','<div class="dashicons-woo-sync"></div>','woo_sync_publisher_control_box','product', 'side','high');
			 }



             function woo_sync_publisher_control_box($post)
            {
              $post_id = get_the_ID();
              if (get_post_type($post_id) != 'product')
                  {
                  return;
                  }
              $value0 = get_post_meta($post_id, 'woo_sync_create_product_now', true);
              
              if (get_option('woo_sync_product_immediately') == 'on')
                  {
                  $value0 = true;
                  }
              
              $woo_sync_Result       = get_post_meta($post_id, 'woo_sync_create_product_now_result', true);
              $woo_sync_Result_error = get_post_meta($post_id, 'woo_sync_create_product_now_result_error', true);
              $woo_sync_wp_error     = get_post_meta($post_id, 'woo_sync_Wordpress_error', true);
              wp_nonce_field('woo_sync_nonce_' . $post_id, 'woo_sync_nonce');	
              ?>
	          <style>
			  div#woo_sync_publisher_control {
              background: #fffeee;
              border: 1px dashed #e4e4e4;
              }
			  
              .dashicons-woo-sync {
              text-align: center;
              color: #5800ff;
              font-size: 30px;
              line-height: 0px;
              margin-bottom: -6px;
              margin-top: -7px;
              margin-right: 10%;
              }			  
              </style>
			  
              <label><input type="checkbox" value="1" <?php checked($value0, true, true); ?> name="woo_sync_create_product_now" /><?php echo __('Publish now!', 'woocommerce-synchronizer'); ?></label></br>
              <b style="color:green;"><?php echo $woo_sync_Result; ?></b><br>
              <b style="color:red;"><?php echo $woo_sync_Result_error; ?></b>
              <b style="color:red;"><?php echo $woo_sync_wp_error; ?></b>
              <?php
            }          
          
          

          /*
		  @add woo sync loader gif to category / tag / attribute pages
		  */
          if (get_option('woo_sync_att_immediately') == 'on') {		  
          add_action('woocommerce_after_add_attribute_fields', 'woo_sync_add_woo_sync_loader_att', 10, 1);
          add_action('woocommerce_after_edit_attribute_fields', 'woo_sync_add_woo_sync_loader_edit_att', 10, 1);
		  if (isset($_GET['taxonomy'])){
              add_action($_GET['taxonomy'].'_add_form_fields', 'woo_sync_add_woo_sync_loader', 10, 1);
              add_action($_GET['taxonomy'].'_edit_form_fields', 'woo_sync_add_woo_sync_loader_edit', 10, 1);
          }
		  }
          
          if (get_option('woo_sync_cat_immediately') == 'on') {
              add_action('product_cat_add_form_fields', 'woo_sync_add_woo_sync_loader', 10, 1);
              add_action('product_cat_edit_form_fields', 'woo_sync_add_woo_sync_loader_edit', 10, 1);
          } 
		  
		  
          if (get_option('woo_sync_tag_immediately') == 'on') {
              add_action('product_tag_add_form_fields', 'woo_sync_add_woo_sync_loader', 10, 1);
              add_action('product_tag_edit_form_fields', 'woo_sync_add_woo_sync_loader_edit', 10, 1);
          }
          
          
           /*
		  @add woo sync loader gif to add new category / tag  page
		  */         
          function woo_sync_add_woo_sync_loader()
          {
          ?> 
          <style>
          @keyframes ckw {
          0% {
             transform: rotate(360deg);
          }
          100% {
            transform: rotate(0deg);
          }
          }
          </style>
          <script>
          jQuery(document).ready(function($) {
          $('#submit').after('<div class="dashicons-woo-sync loader"></div>');
          $('.dashicons-woo-sync.loader').css({ "text-align" : "center" , "font-size" : "40px" , "animation-name" : "ckw" , "animation-duration" : "1s" , "animation-iteration-count" : "infinite" ,  "line-height" : "0" , "display" : "none"});
          $("#submit").click(function(){
          if ($("#tag-name").val().length != 0){	
          $('.dashicons-woo-sync.loader').css({"display" : "block"});
          window.wooInterval = setInterval(function() {
          if ($("#tag-name").val().length === 0){	
          $('.dashicons-woo-sync.loader').css({"display" : "none"});
          }
          }, 100);
          }
          });
          });
          </script>
          <?php
          }
          
 
           /*
		  @add woo sync loader gif to edit new category / tag  page
		  */    
          function woo_sync_add_woo_sync_loader_edit()
          {
          ?> 
          <style>
         @keyframes ckw {
          0% {
             transform: rotate(360deg);
             }
          100% {
             transform: rotate(0deg);
            }
         }
         </style>
         <script>
          jQuery(document).ready(function($) {
          $('#delete-link').after('<div class="dashicons-woo-sync loader"></div>');
          $('.dashicons-woo-sync.loader').css({ "text-align" : "center" , "font-size" : "40px" , "animation-name" : "ckw" , "animation-duration" : "1s" , "animation-iteration-count" : "infinite" ,  "line-height" : "0"  , "display" : "none"});
          $(document).on('submit','form#edittag',function(){
          $('.dashicons-woo-sync.loader').css({"display" : "block"});
          });
          });
          </script>  
          <?php
          }


           /*
		  @add woo sync loader gif to add new attribute  page
		  */   
          function woo_sync_add_woo_sync_loader_att()
          {
          ?> 
          <style>
          @keyframes ckw {
          0% {
             transform: rotate(360deg);
          }
          100% {
            transform: rotate(0deg);
          }
          }
          </style>
          <script>
          jQuery(document).ready(function($) {
          $('#submit').after('<div class="dashicons-woo-sync loader"></div>');
          $('.dashicons-woo-sync.loader').css({ "text-align" : "center" , "font-size" : "40px" , "animation-name" : "ckw" , "animation-duration" : "1s" , "animation-iteration-count" : "infinite" ,  "line-height" : "0" , "display" : "none"});
          $("#submit").click(function(){
          if ($("#attribute_label").val().length != 0){	
          $('.dashicons-woo-sync.loader').css({"display" : "block"});
          window.wooInterval = setInterval(function() {
          if ($("#attribute_label").val().length === 0){	
          $('.dashicons-woo-sync.loader').css({"display" : "none"});
          }
          }, 100);
          }
          });
          });
          </script>
          <?php
          }
          
 
           /*
		  @add woo sync loader gif to edit attribute  page
		  */   
          function woo_sync_add_woo_sync_loader_edit_att()
          {
          ?> 
          <style>
         @keyframes ckw {
          0% {
             transform: rotate(360deg);
             }
          100% {
             transform: rotate(0deg);
            }
         }
         </style>
         <script>
          jQuery(document).ready(function($) {
          $('#submit').after('<div class="dashicons-woo-sync loader"></div>');
          $('.dashicons-woo-sync.loader').css({ "width" : "20%" , "text-align" : "center" , "font-size" : "40px" , "animation-name" : "ckw" , "animation-duration" : "1s" , "animation-iteration-count" : "infinite" ,  "line-height" : "0"  , "display" : "none"});
          $("#submit").click(function(){
          $('.dashicons-woo-sync.loader').css({"display" : "block"});
          });
          });
          </script>
          <?php
          }
          
          
 
          /*
          @admin init
		  @register options meta and active admin notic if woocommerce is not installed yet
          */ 
          add_action('admin_init', function()
              {
              
              register_setting('woo-sync-settings', 'woo_sync_target_url');
              register_setting('woo-sync-settings', 'woo_sync_customer_key');
              register_setting('woo-sync-settings', 'woo_sync_secret_key');
              
              
              register_setting('woo-sync-settings', 'woo_sync_is_local_host');
              register_setting('woo-sync-settings', 'woo_sync_server_ip');
              register_setting('woo-sync-settings', 'woo_sync_server_port');
              register_setting('woo-sync-settings', 'woo_sync_ftp_user');
              register_setting('woo-sync-settings', 'woo_sync_ftp_pass');
              register_setting('woo-sync-settings', 'woo_sync_url_cdn');
              register_setting('woo-sync-settings', 'woo_sync_server_path');
              
              register_setting('woo-sync-settings', 'woo_sync_cat_immediately');
              register_setting('woo-sync-settings', 'woo_sync_tag_immediately');
              register_setting('woo-sync-settings', 'woo_sync_att_immediately');
              register_setting('woo-sync-settings', 'woo_sync_product_immediately');
              register_setting('woo-sync-settings', 'woo_sync_media_immediately');
              register_setting('woo-sync-settings', 'woo_sync_target_access_code');
              
              
              if (is_admin() && current_user_can('activate_plugins') && !is_plugin_active('woocommerce/woocommerce.php'))
                  {
                  add_action('admin_notices', 'woo_sync_child_plugin_notice');
                  }
              
              });
          
          
          
          function woo_sync_child_plugin_notice()
              {
              echo '<div class="error"><p>' . __("To use this plugin, you need to install the Woocommerce.", 'woocommerce-synchronizer') . '<a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank" >install woocommerce</a></p></div>';
              }
 
          
          
          /*
          @create DB tables on plugin activation 
          */ 
          
          register_activation_hook(dirname(dirname(__FILE__)) . '/woo_sync.php', 'woo_sync_options_install');
          function woo_sync_options_install()
              {
              global $wpdb;
              $woo_sync_db_name = $wpdb->prefix . 'woo_sync_future_publish';
              if ($wpdb->get_var("show tables like '$woo_sync_db_name'") != $woo_sync_db_name)
                  {
                  $sql = "CREATE TABLE " . $woo_sync_db_name . " ( 
        `id` mediumint(9) NOT NULL AUTO_INCREMENT, 
        `object_id` mediumtext NOT NULL, 
		`rel_id` mediumtext NOT NULL,
        `object_name` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,		
        `rel_name` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,			
        `object_type` mediumtext NOT NULL, 
        `action` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 
        `status` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 
        `parametrs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 
        `request` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 
        `date` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 		
        UNIQUE KEY id (id) 
        );";
                  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                  dbDelta($sql);
                  }
              
              
              $woo_sync_db_dl = $wpdb->prefix . 'woo_sync_download_list';
              if ($wpdb->get_var("show tables like '$woo_sync_db_dl'") != $woo_sync_db_dl)
                  {
                  $sql = "CREATE TABLE " . $woo_sync_db_dl . " ( 
        `id` mediumint(9) NOT NULL AUTO_INCREMENT, 
        `dl_id` mediumtext NOT NULL, 
		`rel_id` mediumtext NOT NULL,		
		`object_id` mediumtext NOT NULL,
        `object_type` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        `rel_type` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,	
        `upsells` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        `scross` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        `grouped` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,   		
        `parametrs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 		
        `status` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 		
        `date` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
        `creator` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL, 		
        UNIQUE KEY id (id) 
        );";
                  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                  dbDelta($sql);
                  }


              $woo_sync_db_rel = $wpdb->prefix . 'woo_sync_relationships';
              if ($wpdb->get_var("show tables like '$woo_sync_db_rel'") != $woo_sync_db_rel)
                  {
                  $sql = "CREATE TABLE " . $woo_sync_db_rel . " ( 
        `id` mediumint(9) NOT NULL AUTO_INCREMENT, 
        `dl_id` mediumtext NOT NULL, 	
		`object_id` mediumtext NOT NULL,
        `object_type` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,		
        UNIQUE KEY id (id) 
        );";
                  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                  dbDelta($sql);
                  }				  

				  
				  
			if (!get_option("woo_sync_target_uniqid"))	  
             update_option("woo_sync_target_uniqid" , '_'.uniqid());			 

              }
          
          
          
          
          
           /*
          @test rest api connection when user click on "test rest api" in settings page 
          */          
          
          add_action('wp_ajax_nopriv_woo_sync_test_connection_rest', 'woo_sync_test_connection_rest');
          add_action('wp_ajax_woo_sync_test_connection_rest', 'woo_sync_test_connection_rest');
          function woo_sync_test_connection_rest()
              {
              $site = sanitize_text_field($_POST['target']);
              $ck   = sanitize_text_field($_POST['ck']);
              $sk   = sanitize_text_field($_POST['sk']);
 
 
           $tools   = new woo_sync_tools();
           $woosync = new Woo_sync_rest_api();
	   
           $tools->checknetwork('loseInternetConnection' , 'str');
           //woo_sync_createcache($site);

              
              $parametrs = array(
                  'name' => 'woo sync test',
                  'slug' => 'woosynctest'
              );
              
             $api_response    = wp_remote_post($site . '/wp-json/wc/v3/products', array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($ck . ':' . $sk)
                 ),
                 'body' => $parametrs
             ));

              $wp_check_errors = is_object($api_response);
              if ($wp_check_errors == 0)
                  {
                  $body           = $api_response['body'];
                  $product_object = json_decode(woo_sync_json_handler($body));
                  
                  if ($product_object->code != '' and $product_object->message != '')
                      {
                      
                      echo __('Error: Unable to connect to the target site
                      <br><br>
                      This error may occur due to one of the following reasons: <br>
                      1. The consumer key or Secret is not entered correctly. <br>
                      2. Access is not available by .htaccess. <br> <br>

                      Suggested solutions:<br>
                      In the first case, please ensure the accuracy of the entered information and proceed with the tutorial of %how to create consumer key and secret%.<br>
                      In the second case, please login to control panel of your server then go to the Public_html directory(Your wordpress main directory) and find .htaccess file then edit it as follows:<br>
                      In the line after #BEGIN WordPress, place the following code:<br>
                      <br><br>

                     <div class="container" style="direction: ltr !important;text-align: left;"><div class="line number1 index0 alt2"><code class="xml plain">&lt;</code><code class="xml keyword">IfModule</code> <code class="xml plain">mod_rewrite.c&gt;</code></div><div class="line number2 index1 alt1"><code class="xml plain">RewriteEngine On</code></div><div class="line number3 index2 alt2"><code class="xml plain">RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]</code></div><div class="line number4 index3 alt1"><code class="xml plain">&lt;/</code><code class="xml keyword">IfModule</code><code class="xml plain">&gt;</code></div></div>				
					</p>

					<br><br>
                    If the above solutions were not effective, one of the plugins on the target site would be interfering with the creation of this connection. To solve this problem, please first add a plugin <br>
					<a href="https://github.com/WP-API/Basic-Auth" target="_blank">“Basic Authentication Handler”</a> <br>on your online store. Then, instead of the consumer key and Secret, enter the username and password of the target site admin in the above fields.
                    Then, click on the connection test and if the problem persists, contact the plugin developer.
					', 'woocommerce-synchronizer');
                      
                      }
                  else
                      {
                      
                      echo '<p style="color:#21ff21">'.__("The connection to the target store is established.", 'woocommerce-synchronizer').'</p>';
                      
			$api_response = wp_remote_post($site . '/wp-json/wc/v3/products/' . $product_object->id . '/?_method=DELETE&force=true', array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($ck . ':' . $sk)
                 )
             ));
                      
                      }
                  }
              else
                  {
                  
                  $errors = $api_response->get_error_message();
                  if ($errors = 'cURL error 6: Could not resolve host: ' . preg_replace("(^https?://)", "", $site))
                      {
                      $tools = new woo_sync_tools();
                      if ($tools->checknetwork('loseInternetConnection', 'bool'))
                          {
                          echo __('Wrong URL , Make sure to enter the store URL as the following example and avoid placing / at the end of the address:<br> http://example.com or https://example.com', 'woocommerce-synchronizer' );
                          }
                      else
                          {
                          echo __( 'Internet connection is not established...', 'woocommerce-synchronizer' );
                          }
                      }
                  
                  }
              
              die();
              
              }
          
          
          
           /*
          @test FTP connection when user click on "test FTP" in settings page 
          */            
          add_action('wp_ajax_nopriv_woo_sync_test_connection_ftp', 'woo_sync_test_connection_ftp');
          add_action('wp_ajax_woo_sync_test_connection_ftp', 'woo_sync_test_connection_ftp');
          function woo_sync_test_connection_ftp()
              {
              $site = sanitize_text_field($_POST['target']);
              $ck   = sanitize_text_field($_POST['ck']);
              $sk   = sanitize_text_field($_POST['sk']);
              $port = sanitize_text_field($_POST['port']);
              $user = sanitize_text_field($_POST['user']);
              $pass = sanitize_text_field($_POST['pass']);
              
           $tools   = new woo_sync_tools();
           $woosync = new Woo_sync_rest_api();
	   
           $tools->checknetwork('loseInternetConnection' , 'str');
           //woo_sync_createcache($site);
              

	        $api_response    = wp_remote_post($site.'/wp-json/wc/v3/system_status?_method=GET', array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($ck . ':' . $sk)
                 )
             ));
              
              
              $wp_check_errors = is_object($api_response);
              if ($wp_check_errors == 0)
                  {
                  $body           = $api_response['body'];
                  $product_object = json_decode(woo_sync_json_handler($body));
                  
                  if ($product_object->code != '' and $product_object->message != '')
                      {
                      $result['OK']     = "Faild";
                      $result['KIND']   = "auth";
                      $result['reason'] = __('REST Api Connection is not established , please go to top of this page and click on <test rest api connection> to find the possible reasons.', 'woocommerce-synchronizer' );
                      die(json_encode($result));
                      }
                  
                  }
              else
                  {
                  $errors = $api_response->get_error_message();
                  if ($errors = 'cURL error 6: Could not resolve host: ' . preg_replace("(^https?://)", "", $site))
                      {
                      $result['OK']   = "Faild";
                      $result['KIND'] = "auth";
                      $tools          = new woo_sync_tools();
                      if ($tools->checknetwork('loseInternetConnection', 'bool'))
                          {
                          $result['reason'] = __('Wrong TARGET STORE URL , GO to top of this page and Make sure to enter the store URL as the following example and avoid placing / at the end of the address:<br> http://example.com or https://example.com', 'woocommerce-synchronizer' );
                          }
                      else
                          {
                          $result['reason'] = __( 'Internet connection is not established...', 'woocommerce-synchronizer' );
                          }
                      die(json_encode($result));
                      }
                  }
              
              
              $env = json_decode($api_response['body']);
              
              if ($env->environment->site_url)
                  {
                  $host    = parse_url($env->environment->site_url);
                  $conn_id = ftp_connect($host['host'], $port);
                  $login   = ftp_login($conn_id, $user, $pass);
                  ftp_pasv($conn_id, true);
                  if (!$conn_id || !$login)
                      {
                      $result['OK']     = "Faild";
                      $result['KIND']   = "auth";
                      $result['reason'] = sprintf(__('Ftp connection failed. This error may be due to one of the following reasons:<br><br>
                      1. You entered the FTP port incorrectly.<br>
                      2. The username or password is incorrect.<br>
                      <br>
                      Please, if the above information is not available to you, get the host FTP information for %1$s domain from your hosting company, or create a new FTP account according to the above tutorials.' , 'woocommerce-synchronizer') , $site);
                      die(json_encode($result));
                      }
                  }
              
              $mother_path = $env->environment->log_directory;
              
              
              $root = ftp_pwd($conn_id);
              $buff = ftp_nlist($conn_id, $root);
              foreach ($buff as $child)
                  {
                  $eachpos[$child] = strpos($mother_path, $child);
                  }
              $fined_path = array_filter($eachpos);
              $true_path  = array_keys($fined_path, min($fined_path));
              $exploded   = explode($true_path[0], $mother_path);
              
              if ($exploded[1] != '')
                  {
                  if (ftp_chdir($conn_id, dirname($true_path[0] . $exploded[1])))
                      {
                      $result['message'] = '<p style="color:#21ff21">'.__('Connection with ftp was successful.', 'woocommerce-synchronizer' ).'</p>' . __('Click on *Save Changes* now!', 'woocommerce-synchronizer' );
                      $cdir              = ftp_nlist($conn_id, ftp_pwd($conn_id));
                      if (!in_array('woo-sync', $cdir))
                          {
                          ftp_mkdir($conn_id, ftp_pwd($conn_id) . "/woo-sync");
                          
                          $result['OK']     = 'created';
                          $result['SERVER'] = $host['host'];
                          $result['DPATH']  = ftp_pwd($conn_id) . "/woo-sync/";
                          $result['UPATH']  = $env->environment->site_url . '/wp-content/uploads/woo-sync/';
                          echo json_encode($result);
                          }
                      else
                          {
                          $result['OK'] = 'success';
                          $result['SERVER'] = $host['host'];
                          $result['DPATH']  = ftp_pwd($conn_id) . "/woo-sync/";
                          $result['UPATH']  = $env->environment->site_url . '/wp-content/uploads/woo-sync/';						  
                          echo json_encode($result);
                          }
                      
                      }
                  else
                      {
                      $result['OK']     = 'Faild';
                      $result['KIND']   = 'path';
                      $result['reason'] = sprintf(__('FTP connection was successful , but something is wrong with your server!<br>
             In general, if your WordPress is installed as standard, the address of storing information from the localhost in your server should be as follows:<br>
			 <b style="text-align:left; direction:ltr;">../domains/%1$s/public_html/wp-content/uploads/woo-sync/</b><br>
			 In this case, if a file is stored in this path, you should be able to open a link like the following link in browser.<br>
			 <b style="text-align:left; direction:ltr;">%2$s/wp-content/uploads/woo-sync/example.png</b><br>
			 However , in your server, the plugin could not find or create this path. At the moment, there are two fields above that you need to complete.
			 <br><br>
			 To do this, first login to control panel of your server and create a new ftp account (as mentioned in the tutorials)<br>
			 Then copy username and password of your new ftp to "FTP username" and "FTP Password.
			 then go to this path : <br>
			 <b style="text-align:left; direction:ltr;">Wordpress Core -> wp-content -> uploads </b><br>
			 Create a folder called woo-sync. And upload an image in this folder. For example, pic.jpeg .now if your uploaded image name is <b>pic.jpeg</b> you can open <b style="text-align:left; direction:ltr;">%2$s/wp-content/uploads/woo-sync/pic.jpeg</b> in your browser.<br>
			 if you can see your uploaded picture using this link ,back to your server and copy the current path (directory) and place it in the "Upload Path" field that appears above.<br>
			 In the "Upload Url" field, also put the link below : <br>
			 <b style="text-align:left; direction:ltr;">%2$s/wp-content/uploads/woo-sync</b><br>	
            And then save the settings.<br>', 'woocommerce-synchronizer') , $host['host'] , $site);
                      echo json_encode($result);
                      }
                  }
              
              ftp_close($conn_id);
              die();
              }
          
          

  
           /*
          @json handler
          */         
          function woo_sync_json_handler($json)
              {
            //  $pattern = '/\{(?:[^{}]|(?R))*\}/x';
            //  preg_match_all($pattern, $json, $matches);
            //  $wc_object = implode("", $matches[0]);
              return $json;
              }
          
          

          
          
          
          /*
          @ftp uploader , to upload a new media to server when sync products immediately is enabled
          */          
          
          if (!get_option('woo_sync_creating_started') and get_option("woo_sync_is_local_host") === 'on')
              {
              add_filter('wp_generate_attachment_metadata', 'woo_sync_ftp_connection_uploads', 10, 2);
              }
          
          function woo_sync_ftp_connection_uploads($args, $file)
              {

              $upload_dir = wp_upload_dir();
              $upload_url = get_option('upload_url_path');
              $upload_yrm = get_option('uploads_use_yearmonth_folders');
              $settings   = array(
                  'host' => get_option('woo_sync_server_ip'),
                  'port' => get_option('woo_sync_server_port'),
                  'user' => get_option('woo_sync_ftp_user'),
                  'pass' => get_option('woo_sync_ftp_pass'),
                  'cdn' => get_option('woo_sync_url_cdn'),
                  'path' => get_option('woo_sync_server_path'),
                  'base' => $upload_dir['path'] // Basedir on local 
              );
              
              if (get_option('woo_sync_product_immediately') == 'on')
                  {
                  
                  $connection = ftp_connect($settings['host'], $settings['port']);
                  $login      = ftp_login($connection, $settings['user'], $settings['pass']);
                  ftp_pasv($connection, true);
                  if (!$connection || !$login)
                      {
                      die('Connection attempt failed, Check your settings');
                      }
                  function woo_sync_ftp_putAll($conn_id, $src_dir, $dst_dir, $file)
                      {
                      $d = dir($src_dir);

                      if ($file != "." && $file != "..")
                          {
                          if (is_dir($src_dir . "/" . $file))
                              {
                              if (!@ftp_chdir($conn_id, $dst_dir . "/" . $file))
                                  {
                                  ftp_mkdir($conn_id, $dst_dir . "/" . $file); // create directories that do not yet exist
                                  }
                              $created = woo_sync_ftp_putAll($conn_id, $src_dir . "/" . $file, $dst_dir . "/" . $file, $created); // recursive part
                              }
                          else
                              {
                              $upload = ftp_put($conn_id, $dst_dir . "/" . $file, $src_dir . "/" . $file, FTP_BINARY); // put the files
                              if ($upload)
                                  $created[] = $src_dir . "/" . $file;
                              }
                          }
                      //}
                      $d->close();
                      return $created;
                      }
                  $delete = woo_sync_ftp_putAll($connection, $settings['base'], $settings['path'], basename(get_the_guid($file)));
                  return $args;
                  
                  }
              else
                  {
                  global $wpdb;
                  if (!get_option('woo_sync_creating_started'))
                      {
                      $parametrs              = array(
                          "local_file" => $settings['base'] . "/" . basename(get_the_guid($file)),
                          "remote_file" => $settings['path'] . "/" . basename(get_the_guid($file))
                      );
                      $insert2['object_id']   = $file;
                      $insert2['object_name'] = get_the_title($file);
                      $insert2['object_type'] = 'file';
                      $insert2['action']      = 'create';
                      $insert2['parametrs']   = json_encode($parametrs);
                      $insert2['date']        = current_time('Y-m-d h:i:s');
                      
                      
                      $sql = $wpdb->insert($wpdb->prefix . 'woo_sync_future_publish', $insert2);
                      return $args;
                      }
                  }
              
              }
          
          
          
          
 

          /*
          @get media id by url
          */ 
          function woo_sync_get_image_id($image_url)
              {
              global $wpdb;
              $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE guid='%s';", $image_url));
              return $attachment[0];
              }
 

           /*
          @get media id by name
          */ 
          function woo_sync_get_image_id_by_name($image_name)
              {
              global $wpdb;
              $attachment = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value='%s';", $image_name));
              return $attachment[0];
              } 
			  

			  
           /*
          @get id of target store object by object id in localhost
          */ 			  
              function woo_sync_gettargetsiteobjid($id , $type){
                  global $wpdb;
				  if (!is_wp_error($id)){
				  $item = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_relationships where object_type LIKE '".$type."' and object_id = ".$id, ARRAY_A);
				  }
				  if (!empty($item)){
				  $res = $item['dl_id'];
				  }else{
				  $res = '';	  
				  }
				  return $res;				  
              }	

           /*
          @get id of localhost object by object id of target store
          */ 				  
              function woo_sync_getthissiteobjid($id , $type){
                  global $wpdb;
				  if (!is_wp_error($id)){
				  $item = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_relationships where object_type LIKE '".$type."' and dl_id = ".$id, ARRAY_A);
				  }
				  if (!empty($item)){
				  $res = $item['object_id'];
				  }else{
				  $res = '';	  
				  }
				  return $res;
              }				  

           /*
          @Save id of target site object in localhost database
          */ 				  
              function woo_sync_inserttargetsiteobjid($id , $dl_id , $type){
                  global $wpdb;
				  if (!is_wp_error($id)){
				  $item = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_relationships where object_type LIKE '".$type."' and object_id = ".$id, ARRAY_A);
                  
				  if (empty($item)){
                               $insert2['object_id']   = $id;
                               $insert2['dl_id']       = $dl_id;
                               $insert2['object_type'] = $type;							   
                               $sql                    = $wpdb->insert($wpdb->prefix . 'woo_sync_relationships', $insert2);	                  
				  }else{
                       $update['dl_id']     = $dl_id;
                       $where['id']         = $item['id'];
                       $sql                 = $wpdb->update($wpdb->prefix . 'woo_sync_relationships', $update, $where);                  
				  }
				  }
				  return;
				  
              }				  
  

           /*
          @delete id of target site object from localhost database
          */ 	 
              function woo_sync_deletetargetsiteobjid($id , $type){
                  global $wpdb;
				  $item = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}woo_sync_relationships where object_type LIKE '".$type."' and dl_id = ".$id, ARRAY_A);
				  if (!empty($item)){
		          $wherei['id'] = $item['id'];
	   	          $wpdb->delete($wpdb->prefix . 'woo_sync_relationships', $wherei); 				  
              }
			  }
  
  
          /*
          @convert numbers , if number is zero convert that number to "No" , we use this function in downloading results 
          */           
          function woo_sync_converting_numbers($srting, $toPersian = true)
              {
                  if ($srting == 0){
				   return 'No';	  
				  }else{
				  return $srting;
				  }
              }
			    
              			  
			  
?>