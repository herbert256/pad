<?php

  $pad_lvl++;
  
  if ( isset ( $pad_current [$pad_lvl] ) )
    unset ( $pad_current [$pad_lvl] );

  include 'inits.php';

  $pad_tag_result = include 'tag.php';
  
  if ( $pad_tag_result === NULL )
    return NULL;

  $pad_parameters [$pad_lvl] ['default_data'] = pad_is_default_data ( $pad_data [$pad_lvl] );
  
  $pad_options = 'level_start';
  include PAD . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( isset ( $pad_parms_tag ['parent'] ) )
    include PAD . 'options/parent.php';
  elseif ( $pad_pair === FALSE and $pad_tag_type == 'data' and trim($pad_base [$pad_lvl]) === '' and ! isset ( $pad_parms_tag ['print'] ) )
    include PAD . 'options/parent.php';

  if ( $pad_trace ) 
    include 'trace/start.php';

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';

  return $pad_tag_result;
  
?>