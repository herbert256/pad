<?php

  $pad_parameters [$pad_lvl] ['tag_count']++;

  $pad_data [$pad_lvl]     = [];
  $pad_data [$pad_lvl] [1] = [];

  if ( $pad_lvl == 1 )
    $pad_data [1] [1] = &$GLOBALS;

  $pad_content_store_name = pad_tag_parm ('content');
  $pad_data_store_name    = pad_tag_parm ('data'); 
  $pad_flag_store_name    = pad_tag_parm ('flag');

  include PAD_HOME . "level/type.php";

  if ( $pad_walk == 'next' and isset ( $pad_parms_tag ['toDataStore'] ) )
     pad_error ('@toDataStore can not be used together with the walking method');

  if ( $pad_tag_result === NULL or ! isset ($pad_tag_result) or ! isset ($pad_data[$pad_lvl]) or $pad_data[$pad_lvl] === NULL ) {
    $pad_tag_result = $pad_content = $pad_false = $pad_tag_ob = '';
    $pad_data [$pad_lvl] = [];
  } 
  
  if ( is_object   ( $pad_tag_result ) ) $pad_tag_result = (array) $pad_tag_result;
  if ( is_resource ( $pad_tag_result ) ) $pad_tag_result = (array) $pad_tag_result;

  if ( is_object   ( $pad_data [$pad_lvl] ) ) $pad_data [$pad_lvl] = (array) $pad_data [$pad_lvl];
  if ( is_resource ( $pad_data [$pad_lvl] ) ) $pad_data [$pad_lvl] = (array) $pad_data [$pad_lvl];

  if ( ! is_array ( $pad_data [$pad_lvl] ) )  {
    $pad_tmp                            = $pad_data [$pad_lvl];
    $pad_data [$pad_lvl]                = [];
    $pad_data [$pad_lvl] [1] [ 'value'] = $pad_tmp;
  }

  if ( is_array($pad_tag_result) )
    $pad_data [$pad_lvl] = $pad_tag_result;

  if ( $pad_data_store_name ) {
    if ( count($pad_data [$pad_lvl]) == 1 and isset($pad_data[$pad_lvl][1]) and count($pad_data[$pad_lvl][1]) == 0 )
      $pad_data [$pad_lvl] = $pad_data_store [$pad_data_store_name];
    else
      $pad_data [$pad_lvl] = array_merge($pad_data [$pad_lvl], pad_data_store [$pad_data_store_name]);
  }

  if     ( is_array($pad_tag_result) )  $pad_base [$pad_lvl] = ( count($pad_data [$pad_lvl]) ) ? $pad_content : $pad_false;
  elseif ( $pad_tag_result === FALSE )  $pad_base [$pad_lvl] = $pad_false;
  elseif ( $pad_tag_result === TRUE  )  $pad_base [$pad_lvl] = $pad_content;
  else                                  $pad_base [$pad_lvl] = $pad_content . $pad_tag_result;

  $pad_base [$pad_lvl] .= $pad_tag_ob;

  if ( $pad_content_store_name ) 
    $pad_base [$pad_lvl] .= $pad_content_store [$pad_content_store_name] ;

  if ( $pad_tag_result === NULL )
    $pad_html [$pad_lvl] = '';
 
  $pad_walks [$pad_lvl] = $pad_walk;  

  include PAD_HOME . 'level/after.php';

  if ( ! count ($pad_data [$pad_lvl] ) ) {
    $pad_base [$pad_lvl] = $pad_false;
    $pad_data [$pad_lvl] [1] = [];
  }

  reset ( $pad_data[$pad_lvl] );
  
?>