<?php

  foreach ( $pad_seq_filter_list as $pad_seq_filter_name ) {

    $pad_seq_parm = $pad_parms_tag [$pad_seq_filter_name];

    $pad_seq_filter_check = include PAD . "sequence/types/$pad_seq_filter_name/filter.php"; 

    if ( $pad_seq_filter_check === FALSE )
      return FALSE;

  }

  return $pad_seq_loop;

?>