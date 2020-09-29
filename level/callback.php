<?php

  $pad_include_file = PAD_APP . "callbacks/" . $pad_parms_tag ['callback'] . ".php";

  if ( $pad_callback == 'exit_occurrence' ) 
    $pad_content = $pad_html [$pad_lvl];
  elseif ( $pad_callback == 'exit_tag' ) 
    $pad_content = $pad_result [$pad_lvl];

  include PAD_HOME . 'level/app.php';

  if ( $pad_callback == 'exit_occurrence' ) 
    $pad_html [$pad_lvl] = $pad_content;
  elseif ( $pad_callback == 'exit_tag' ) 
    $pad_result [$pad_lvl] = $pad_content;

?>