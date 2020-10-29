<?php

  $pad_dump_txt = $pad_parm;

  $pad_lvl--;

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  pad_dump ($pad_dump_txt);

  pad_exit();
  
?>