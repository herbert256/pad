<?php

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_content = $pad_result[$pad_lvl];

  $pad_walk = 'end';
  include PAD_HOME . "level/type.php";

  $pad_result[$pad_lvl] = $pad_content;

?>