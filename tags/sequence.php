<?php

  $pad_tag = 'loop';

  if ( $pad_parm and pad_file_exists ( PAD_HOME . "sequence/types/$pad_parm" ))
    $pad_tag = $pad_parm;
  else
    foreach ( $pad_parms_tag as $pad_k => $pad_v ) 
      if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_k" ) ) {
        $pad_tag = $pad_k;
        break;
      }

  $pad_sequence = include PAD_HOME . "sequence/sequence.php";

  $pad_tag = 'sequence';

  return $pad_sequence;

?>