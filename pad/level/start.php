<?php

  $pad_lvl++;
  
  include 'inits.php';

  $pad_tag_result = include 'tag.php';
  
  include 'trace/start.php';

  if ( $pad_tag_result === NULL )
    return NULL;

  $pad_options = 'level_start';
  include PAD . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';

  return $pad_tag_result;
  
?>