<?php

  if ( is_array ($pad_seq_checks) )
    return;

  $pad_seq_tmp = pad_explode ($pad_seq_checks, '|');

  foreach ( $pad_seq_tmp as $pad_seq_work ) {

    $pad_seq_tmp2 = pad_explode ($pad_seq_work, '=');

    $pad_seq_checks [$pad_seq_tmp2[0]] = $pad_seq_tmp2[1]??''; 

    $GLOBALS ['pad_seq_' . $pad_seq_tmp2[0] ] = $pad_seq_checks [$pad_seq_tmp2[0]]; 

  }


?>