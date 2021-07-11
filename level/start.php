<?php

  $pad_lvl++;
  $pad_lvl_cnt++;
  
  if ( isset ( $pad_current [$pad_lvl] ) )
    unset ( $pad_current [$pad_lvl] );

  include 'inits.php';

  $pad_tag_result = include 'tag.php';

  if ($pad_lvl > 1) 
    pad_make_data ( $pad_data[$pad_lvl] );
  
  $pad_options = 'level_start';
  include PAD_HOME . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD_HOME . 'callback/init.php' ;

  if ( isset ( $pad_parms_tag ['parent'] ) )
    include PAD_HOME . 'options/parent.php';
  elseif ( $pad_pair === FALSE and $pad_tag_type == 'data' and trim($pad_base [$pad_lvl]) === '' and ! isset ( $pad_parms_tag ['print'] ) )
    include PAD_HOME . 'options/parent.php';

  if ( $pad_trace ) 
    include 'trace/start.php';

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD_HOME . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';

  return $pad_tag_result;
  
?>