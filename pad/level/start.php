<?php
  
  $pad++;
  $pad_lvl_cnt++;

  $pad_content = $pad_true [$pad];
  include 'type_go.php';
  $pad_true [$pad] = $pad_content;
  
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pad_parms [$pad] ['hit']     = $pad_hit;
  $pad_parms [$pad] ['null']    = $pad_null;
  $pad_parms [$pad] ['else']    = $pad_else;
  $pad_parms [$pad] ['array']   = $pad_array;
  $pad_parms [$pad] ['text']    = $pad_text;

  $pad_parms [$pad] ['default'] = pad_is_default_data ( $pad_data [$pad] );

  include PAD . "options/go/start.php";
  
  include 'trace/start.php';

  if ( isset($pad_prms_tag ['callback']) and ! isset($pad_prms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pad_data[$pad] ) )
    include PAD . 'occurrence/start.php';
  
?>