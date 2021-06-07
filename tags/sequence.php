<?php

  if ( $pad_parm and pad_file_exists ( PAD_HOME . "sequence/types/$pad_parm" )) {
    $pad_tag = $pad_parm;
    goto go;
  }

  foreach ( $pad_parms_tag as $pad_tag => $pad_tag_value) 
    if ( $pad_tag <> 'step' and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag" ) ) 
      goto go;

  $pad_tag = 'step';
  if ( ! isset ( $pad_parms_tag ['step'] ) )
    if ($pad_parm and is_numeric($pad_parm) )
      $pad_parms_tag ['step'] = $pad_parm;
    else
      $pad_parms_tag ['step'] = 1;

go:

  $pad_sequence = include PAD_HOME . 'sequence/sequence.php';

  $pad_tag = 'sequence';

  return $pad_sequence;

?>