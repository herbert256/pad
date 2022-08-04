<?php

  foreach ( $pad_parms [$pad] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_walk = 'end';

  $pad_content = $pad_result [$pad];

  include PAD . "level/type_go.php"; 
  include PAD . "level/flags.php";
  include PAD . "level/base.php";

  $pad_result [$pad] = $pad_content;

?>