<?php

  foreach ( $pad_seq_remove_list as $pad_seq_remove_name => $pad_seq_remove_value ) {

    pad_seq_set ( $pad_seq_remove_name, $pad_seq_remove_value );

    $pad_seq_remove_check = include PAD . "sequence/types/$pad_seq_remove_name/filter.php"; 

    if ( $pad_seq_remove_check === TRUE )
      return FALSE;

  }

  return $pad_seq_loop;

?>