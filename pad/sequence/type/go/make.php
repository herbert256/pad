<?php

  $pad_seq_parm_save = $pad_seq_parm;

  foreach ( $pad_seq_make as $pad_seq_make_name ) {
    $pad_seq_parm = $pad_parms_tag [$pad_seq_make_name];
    $pad_seq_loop = include PAD . "sequence/types/$pad_seq_make_name/make.php"; 
  }

  $pad_seq_parm = $pad_seq_parm_save;

  return $pad_seq_loop;

?>