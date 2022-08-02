<?php

  $pad_lvl++;
  
  include 'inits.php';

  include 'tag.php';
  
  include 'trace/start.php';

  $pad_options = 'level_start';
  include PAD . "options/go/options.php";

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD . 'callback/init.php' ;

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/start.php';
  else
    $pad_html [$pad_lvl] = '';
  
?>