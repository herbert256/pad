<?php

  $pad_tag_save = $pad_tag;

  if ( $pad_parm and pad_file_exists ( PAD_HOME . "sequence/types/$pad_parm" ))

    $pad_tag = $pad_parm;

  else {

    $pad_tag = array_key_first ($pad_parms_tag);

    if ( ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag" ) ) {
      $pad_tag = 'step';
      if ( ! isset ( $pad_parms_tag ['step'] ) )
        if ($pad_parm and is_numeric($pad_parm) )
          $pad_parms_tag ['step'] = $pad_parm;
        else
          $pad_parms_tag ['step'] = 1;
    }

  }

  $pad_seq = include PAD_HOME . 'sequence/sequence.php';

  $pad_tag = $pad_tag_save;

  return $pad_seq;

?>