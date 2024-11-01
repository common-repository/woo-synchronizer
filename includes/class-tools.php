<?php
          /*
          @tools class
          */
          class woo_sync_tools
            {
				
				

			
              function logresults($msg, $action)
                {
                  $myfile = fopen(plugin_dir_path(__FILE__) . 'synchronizer/local/results.txt', $action) or die("Unable to open file!");
                  $txt = $msg . '<br>';
                  fwrite($myfile, $txt);
                  fclose($myfile);
                }
				
              function logerrors($msg, $action)
                {
                  $myfile = fopen(plugin_dir_path(__FILE__) . 'synchronizer/local/error.txt', $action) or die("Unable to open file!");
                  $txt = $msg . '<br>';
                  fwrite($myfile, $txt);
                  fclose($myfile);
                }
				
				
              function checknetwork($msg, $output)
                {
                  $connected = fopen("http://www.google.com:80/", "r");
                  if (!$connected)
                    {
                      if ($output === 'bool')
                        {
                          return false;
                        }
                      else
                        {
                          die($msg);
                        }
                    }
                  else
                    {
                      if ($output === 'bool')
                        {
                          return true;
                        }
                    }
                }
				
				
              function getobjectimg($url, $media_id)
                {
					
				if ($url){	
                  global $wpdb;
                  $media_check = woo_sync_getthissiteobjid($media_id , 'media');
                  if ($media_check)
                    {
                      $id = $media_check;
                    }
                  else
                    {
                      remove_filter('wp_generate_attachment_metadata', 'woo_sync_ftp_connection_uploads');
                      $post_id    = 0;
                      $tmp        = download_url($url);
                      $file_array = array(
                          'name' => basename($url),
                          'tmp_name' => $tmp
                      );
                      $id         = media_handle_sideload($file_array, $post_id);
                      if ($id)
                        {
						  woo_sync_inserttargetsiteobjid($id , $media_id , 'media');
                        }
                    }
                  return $id;
				}else{
				return '';	
				}
                }
						
			
            }
		
?>