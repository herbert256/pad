<?php

  $pad_lvl++;
  
  include 'inits.php';
  include 'type.php';
  include 'options.php';
  include 'flags.php';
  include 'base.php';
  include 'data.php';

  $pad_parameters [$pad_lvl] ['content'] = $pad_content;
  $pad_parameters [$pad_lvl] ['false']   = $pad_false;
  $pad_parameters [$pad_lvl] ['true']    = $pad_true;
  $pad_parameters [$pad_lvl] ['null']    = $pad_null;
  $pad_parameters [$pad_lvl] ['else']    = $pad_else;
  $pad_parameters [$pad_lvl] ['array']   = $pad_array;
  $pad_parameters [$pad_lvl] ['text']    = $pad_text;
  $pad_parameters [$pad_lvl] ['default'] = pad_is_default_data ( $pad_data [$pad_lvl] );
  
  if ( ! $pad_trace_level )
    include 'trace/start.php';

  include PAD . "options/go/start.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/start.php';
  
?>