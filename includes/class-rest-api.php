<?php
     /*
     @ Woocommerce rest api class , post , put , get or delete based on https://woocommerce.github.io/woocommerce-rest-api-docs/#introduction
     */
	 
class Woo_sync_rest_api{
  
  private $site;
  private $ck; 
  private $sk; 
  private $uniq;  
 
  public function __construct() {
        $this->site = get_option('woo_sync_target_url');
        $this->ck = get_option('woo_sync_customer_key');
        $this->sk = get_option('woo_sync_secret_key');
        $this->uniq = get_option('woo_sync_target_uniqid');		
  }
  
 

  public function post($endpoint , $parametrs){

            $api_response    = wp_remote_post($this->site . '/wp-json/wc/v3/'.$endpoint.'?Uniqid='.$this->uniq, array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($this->ck . ':' . $this->sk)
                 ),
                 'body' => $parametrs,
				 'timeout' => '20'
             ));
			 
    return $api_response;
  }
  

   
  public function put($endpoint , $parametrs){
	  
            $api_response    = wp_remote_post($this->site . '/wp-json/wc/v3/'.$endpoint.'/?_method=PUT&Uniqid='.$this->uniq, array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($this->ck . ':' . $this->sk)
                 ),
                 'body' => $parametrs,
				 'timeout' => '20'
             ));
			 
    return $api_response;
  }  
  
  

  public function get($endpoint , $parametrs , $extraquery){
	  
            $api_response    = wp_remote_post($this->site . '/wp-json/wc/v3/'.$endpoint.'/?_method=GET&Uniqid='.$this->uniq.$extraquery, array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($this->ck . ':' . $this->sk)
                 ),
                 'body' => $parametrs,
				 'timeout' => '20'
             ));
			 
    return $api_response;
  }
  

 
  public function delete($endpoint , $parametrs , $extraquery){
	  
            $api_response    = wp_remote_post($this->site . '/wp-json/wc/v3/'.$endpoint.'/?_method=DELETE&Uniqid='.$this->uniq.$extraquery, array(
                 'headers' => array(
                     'Authorization' => 'Basic ' . base64_encode($this->ck . ':' . $this->sk)
                 ),
                 'body' => $parametrs,
				 'timeout' => '20'
             ));
			 
    return $api_response;
  }
  
  
}	 
 
 
 
 


class Woo_sync_target_rest_api{
  
  private $site;
  private $accesscode; 
  private $uniq;  
 
  public function __construct() {
        $this->site = get_option('woo_sync_target_url');
        $this->accesscode = get_option('woo_sync_target_access_code');	
        $this->uniq = get_option('woo_sync_target_uniqid');			
  }
  

  public function post($endpoint , $parametrs){
    $api_response = wp_remote_get($this->site. '/wp-json/woo_sync_target/v1/'.$endpoint.'?WooSyncAceesPass='.$this->accesscode.'&POST_ID='.$parametrs.'&Uniqid='.$this->uniq , array(
	'timeout' => '20'
	));		 
    return $api_response;
  }


  public function get($endpoint){
       $api_response    = wp_remote_get($this->site. '/wp-json/woo_sync_target/v1/'.$endpoint.'?WooSyncAceesPass='.$this->accesscode.'&Uniqid='.$this->uniq , array(
	   'timeout' => '20'
	   ));
	   return $api_response['body'];	    
  }
  
}	  
?>