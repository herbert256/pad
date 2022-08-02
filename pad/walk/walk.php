<?php
     
  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;
  
  $pad_tag_result = include PAD . "level/type.php";
  include PAD . "level/flags.php";

?> 