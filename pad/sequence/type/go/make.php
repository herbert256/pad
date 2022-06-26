<?php

  foreach ( $pad_seq_make_list as $pad_seq_make_name ) {
    $pad_seq_parm = $pad_parms_tag [$pad_seq_make_name];
    $pad_seq_loop = include PAD . "sequence/types/$pad_seq_make_name/make.php"; 
  }

  return $pad_seq_loop;

?>