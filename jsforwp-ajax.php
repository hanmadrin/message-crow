<?php
/*
   Plugin Name: Messenger
   Version: 1.0.0
   Author: Zac Gordon
   Author URI: https://twitter.com/zgordon
   Description: An example of how to do a simple AJAX call in WordPress
   Text Domain: jsforwp-jquery-ajax
   License: GPLv3
*/

defined( 'ABSPATH' ) or die( 'No direct access!' );


$likes = get_option( 'jsforwp_likes' );
if ( null == $likes  ) {
  add_option( 'jsforwp_likes', 0 );
  $likes = 0;
}

if(!get_option('messenger_database_exists',false))
{
    global $wpdb;
    $wpdb->query('CREATE TABLE messenger_dataslot_a (sl int,data varchar(10));');
    $wpdb->query('CREATE TABLE messenger_dataslot_b (sl int,data varchar(30));');
    $wpdb->query('CREATE TABLE messenger_dataslot_c (sl int,data varchar(50));');
    $wpdb->query('CREATE TABLE messenger_dataslot_d (sl int,data varchar(100));');
    $wpdb->query('CREATE TABLE messenger_dataslot_e (sl int,data varchar(200));');
    $wpdb->query('CREATE TABLE messenger_dataslot_f (sl int,data varchar(300));');
    $wpdb->query('CREATE TABLE messenger_dataslot_g (sl int,data varchar(400));');
    $wpdb->query('CREATE TABLE messenger_dataslot_h (sl int,data varchar(500));');
    $wpdb->query('CREATE TABLE messenger_dataslot_i (sl int,data varchar(1000));');
    $wpdb->query('CREATE TABLE messenger_dataslot_j (sl int,data varchar(2000));');
    $wpdb->query('CREATE TABLE messenger_dataslot_k (sl int,data varchar(3000));');
    $wpdb->query('CREATE TABLE messenger_dataslot_l (sl int,data varchar(4000));');
    $wpdb->query('CREATE TABLE messenger_dataslot_m (sl int,data varchar(5000));');
    update_option('messenger_database_exists',1,null);
}
//delete_option('messenger_database_exists');

function jsforwp_frontend_scripts() {
    if(is_user_logged_in()){
        
    wp_enqueue_style('jsforwp-frontend-css',plugins_url( '/assets/css/messenger.css', __FILE__ ), [], time());
  wp_enqueue_script(
    'jsforwp-frontend-js',
    plugins_url( '/assets/js/frontend-main.js', __FILE__ ),
    ['jquery'],
    time(),
    true
  );

  // Change the value of 'ajax_url' to admin_url( 'admin-ajax.php' )
  // Change the value of 'total_likes' to get_option( 'jsforwp_likes' )
  // Change the value of 'nonce' to wp_create_nonce( 'jsforwp_likes_nonce' )
  
  
  wp_localize_script(
    'jsforwp-frontend-js',
    'messenger_preData',
    [
      'ajax_url'    => admin_url( 'admin-ajax.php' ),
      'total_likes' => get_option( 'jsforwp_likes' ),
      'nonce'       => wp_create_nonce( 'jsforwp_likes_nonce' ),
      'previous_message' => previous_messages()
    ]
  );
    }
}
add_action( 'wp_enqueue_scripts', 'jsforwp_frontend_scripts' );

function previous_messages()
{
    $username=wp_get_current_user()->user_login;
    global $wpdb;
    $maxsl=$wpdb->get_results("SELECT MAX(sl) FROM  messenger_messages_data WHERE `username`='".$username."' ;");
    $maxsl=$maxsl[0];
    $maxsl=$maxsl->{'MAX(sl)'};
    $previous_message=$wpdb->get_results("SELECT * FROM messenger_messages_data WHERE `sl` BETWEEN ".($maxsl-9)." AND ".$maxsl." AND `username`='".$username."'");
    for($i=0;$i<10;$i++)
    {
        //$message=$wpdb->get_results("SELECT `data` FROM `messenger_dataslot_".$previous_message[i]->dataslot." WHERE `sl`=".intval($previous_message[i]->datasl).";");
        $previous_message[$i]->datasl=1;
    }
    return $previous_message;
}

function jsforwp_add_like( ) {

  // Change the parameter of check_ajax_referer() to 'jsforwp_likes_nonce'
  check_ajax_referer( 'jsforwp_likes_nonce' );
  $likes = intval( get_option( 'jsforwp_likes' ) );
  $new_likes = $likes + 1;
  $success = update_option( 'jsforwp_likes', $new_likes );

  if( true == $success ) {
    $response['total_likes'] = $new_likes;
    $response['type'] = 'success';
  }
    
  $response = json_encode( $response );
  echo $response;
  die();

}
function messenger_sql_string($string)
{
    $string=str_replace('\\','\\\\',$string);
    $string=str_replace("'","''",$string);
    return $string;
}
function messenger_get_slot($string)
{
    $l=strlen($string);
    if($l>=0 && $l<=10)return 'a';
    if($l>=11 && $l<=30)return 'b';
    if($l>=31 && $l<=50)return 'c';
    if($l>=51 && $l<=100)return 'd';
    if($l>=101 && $l<=200)return 'e';
    if($l>=201 && $l<=300)return 'f';
    if($l>=301 && $l<=400)return 'g';
    if($l>=401 && $l<=500)return 'h';
    if($l>=501 && $l<=1000)return 'i';
    if($l>=1001 && $l<=2000)return 'j';
    if($l>=2001 && $l<=3000)return 'k';
    if($l>=3001 && $l<=4000)return 'l';
    if($l>=4001 && $l<=5000)return 'm';
}
function process_recieved_message()
{
  check_ajax_referer( 'jsforwp_likes_nonce' );
  $likes = intval( get_option( 'jsforwp_likes' ) );
  $new_likes = $likes + 1;
  $success = update_option( 'jsforwp_likes', $new_likes );
  if( true == $success ) {
    $response['total_likes'] = $new_likes;
    $response['type'] = 'success';
  }
  //START HERE
  global $wpdb;
  $message=$_POST['data'];
  $message=sql_string($message);
  $dataslot=messenger_get_slot($message);
  $datasl=$wpdb->get_results("SELECT MAX(sl) FROM  messenger_dataslot_".$dataslot.";");
  $datasl=$datasl[0];
  $datasl=$datasl->{'MAX(sl)'};
  if($datasl==''){$datasl=1;}else{$datasl++;}
  $wpdb->query("INSERT INTO `messenger_dataslot_".$dataslot."`(`sl`) VALUES (".intval($datasl).")");
  $wpdb->query("UPDATE `messenger_dataslot_".$dataslot."` SET `data`='".$message."' WHERE `sl`=".intval($datasl).";");
  $username=wp_get_current_user()->user_login;
  $sl=$wpdb->get_results("SELECT MAX(sl) FROM  messenger_messages_data WHERE `username`='".$username."' ;");
  $sl=$sl[0];
  $sl=$sl->{'MAX(sl)'};
  if($sl==''){$sl=1;}else{$sl++;}
  $wpdb->query("INSERT INTO `messenger_messages_data` (`sl`,`username`) VALUES (".intval($sl).",'".$username."')");
  $wpdb->query("UPDATE `messenger_messages_data` SET `user_server`=0,`exist`=1,`time`=".time().",`dataslot`='".$dataslot."',`datasl`=".$datasl." WHERE `sl`=".intval($sl)." AND `username`='".$username."';");
  $response['linker']=$_POST['linker'];
  $response['sl']=$sl;
  $response = json_encode( $response );
  echo $response;
  die();
}
add_action( 'wp_ajax_jsforwp_add_like', 'jsforwp_add_like' );
add_action('wp_ajax_recieve_message','process_recieved_message');

//require_once( 'assets/lib/plugin-page.php' );
