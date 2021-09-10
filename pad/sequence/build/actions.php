<?php

  if ( is_array ($pad_seq_actions) )
    return;

  $pad_seq_tmp = pad_explode ($pad_seq_actions, '|');

  foreach ( $pad_seq_tmp as $pad_seq_work ) {

    $pad_seq_tmp2 = pad_explode ($pad_seq_work, '=');

    $pad_seq_actions [$pad_seq_tmp2[0]] = $pad_seq_tmp2[1]??''; 

    $GLOBALS ['pad_seq_' . $pad_seq_tmp2[0] ] = $pad_seq_actions [$pad_seq_tmp2[0]]; 

  }


?>