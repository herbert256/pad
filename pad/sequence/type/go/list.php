<?php

  $pad_seq_list      = $pad_seq_loop;
  $pad_seq_list_last = $pad_seq_loop;

  foreach ( $GLOBALS["pad_seq_$pad_seq_opr_name"."_list"] as $pad_seq_list_name => $pad_seq_list_value ) {

    pad_seq_set ( $pad_seq_list_name, $pad_seq_list_value );

    $pad_seq_loop = $pad_seq_list;

    if ( $pad_seq_opr_name== 'make' )
      $pad_seq_list = include PAD . "sequence/types/$pad_seq_list_name/make.php"; 
    else
      $pad_seq_list = include PAD . "sequence/types/$pad_seq_list_name/filter.php"; 

    if     ( $pad_seq_opr_name == 'keep'   and $pad_seq_list === FALSE ) return FALSE;
    elseif ( $pad_seq_opr_name == 'remove' and $pad_seq_list === TRUE  ) return FALSE;
    elseif ( $pad_seq_opr_name == 'keep'   and $pad_seq_list === TRUE  ) $pad_seq_list = $pad_seq_list_last;
    elseif ( $pad_seq_opr_name == 'remove' and $pad_seq_list === FALSE ) $pad_seq_list = $pad_seq_list_last;
    elseif ( $pad_seq_list === NULL                                    ) return NULL;
    elseif ( $pad_seq_list === INF                                     ) return NULL; 
    elseif ( $pad_seq_list === NAN                                     ) return NULL; 
    elseif ( $pad_seq_list === TRUE                                    ) $pad_seq_list = $pad_seq_list_last;
    elseif ( $pad_seq_list === FALSE                                   ) return FALSE;

   $pad_seq_list_last = $pad_seq_list;

  }

  return $pad_seq_list;

?>