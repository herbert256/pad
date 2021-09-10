<?php

  $pad_seq_fromto = ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) );

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) 
    $pad_seq_fromto_max =  ( ( intval( $pad_parms_tag ['to'] ?? PHP_INT_MAX ) ) - ( intval ( $pad_parms_tag ['from'] ?? 1 ) ) ) + 1;
  else
    $pad_seq_fromto_max = 0;

?>