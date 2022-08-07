<?php

  $pSeq_list      = $pSeq_loop;
  $pSeq_list_last = $pSeq_loop;

  foreach ( $GLOBALS["pSeq_$pSeq_opr_name"."_list"] as $pSeq_list_name => $pSeq_list_value ) {

    pSeq_set ( $pSeq_list_name, $pSeq_list_value );

    $pSeq_loop = $pSeq_list;

    if ( $pSeq_opr_name== 'make' )
      $pSeq_list = include PAD . "sequence/types/$pSeq_list_name/make.php"; 
    else
      $pSeq_list = include PAD . "sequence/types/$pSeq_list_name/filter.php"; 

    if     ( $pSeq_opr_name == 'keep'   and $pSeq_list === FALSE ) return FALSE;
    elseif ( $pSeq_opr_name == 'remove' and $pSeq_list === TRUE  ) return FALSE;
    elseif ( $pSeq_opr_name == 'keep'   and $pSeq_list === TRUE  ) $pSeq_list = $pSeq_list_last;
    elseif ( $pSeq_opr_name == 'remove' and $pSeq_list === FALSE ) $pSeq_list = $pSeq_list_last;
    elseif ( $pSeq_list === NULL                                    ) return NULL;
    elseif ( $pSeq_list === INF                                     ) return NULL; 
    elseif ( $pSeq_list === NAN                                     ) return NULL; 
    elseif ( $pSeq_list === TRUE                                    ) $pSeq_list = $pSeq_list_last;
    elseif ( $pSeq_list === FALSE                                   ) return FALSE;

   $pSeq_list_last = $pSeq_list;

  }

  return $pSeq_list;

?>