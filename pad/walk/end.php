<?php

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_walk = 'end';

  $pad_content = $pad_result [$pad_lvl];

  include PAD . "level/type_go.php"; 
  include PAD . "level/flags.php";
  include PAD . "level/base.php";

  $pad_result [$pad_lvl] = $pad_content;

?>