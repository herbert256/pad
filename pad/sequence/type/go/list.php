<?php

  $padSeq_list      = $padSeq_loop;
  $padSeq_list_last = $padSeq_loop;

  foreach ( $GLOBALS["pSeq_$padSeq_opr_name"."_list"] as $padSeq_list_name => $padSeq_list_value ) {

    pSeq_set ( $padSeq_list_name, $padSeq_list_value );

    $padSeq_loop = $padSeq_list;

    if ( $padSeq_opr_name== 'make' )
      $padSeq_list = include PAD . "sequence/types/$padSeq_list_name/make.php"; 
    else
      $padSeq_list = include PAD . "sequence/types/$padSeq_list_name/filter.php"; 

    if     ( $padSeq_opr_name == 'keep'   and $padSeq_list === FALSE ) return FALSE;
    elseif ( $padSeq_opr_name == 'remove' and $padSeq_list === TRUE  ) return FALSE;
    elseif ( $padSeq_opr_name == 'keep'   and $padSeq_list === TRUE  ) $padSeq_list = $padSeq_list_last;
    elseif ( $padSeq_opr_name == 'remove' and $padSeq_list === FALSE ) $padSeq_list = $padSeq_list_last;
    elseif ( $padSeq_list === NULL                                    ) return NULL;
    elseif ( $padSeq_list === INF                                     ) return NULL; 
    elseif ( $padSeq_list === NAN                                     ) return NULL; 
    elseif ( $padSeq_list === TRUE                                    ) $padSeq_list = $padSeq_list_last;
    elseif ( $padSeq_list === FALSE                                   ) return FALSE;

   $padSeq_list_last = $padSeq_list;

  }

  return $padSeq_list;

?>