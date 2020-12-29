<?php
    
  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_walk = 'next';
  include PAD_HOME . 'level/tag.php';

  if ( $pad_walk == 'next' )
    return include PAD_HOME . 'occurrence/start.php';
  
?> 