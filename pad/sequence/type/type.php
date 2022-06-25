<?php

  if ( $pad_seq_sequence ) {

    if     ( isset($pad_data_store [$pad_seq_sequence]) )  
      $pad_seq_for = $pad_data_store [$pad_seq_sequence];
    elseif ( pad_check_range ( $pad_seq_sequence )       )  
      $pad_seq_for = pad_get_range ( $pad_seq_sequence, $pad_seq_inc );
    else                                                   
      $pad_seq_for = pad_make_data ( $pad_seq_sequence, '', $pad_seq_name, 0 );

    return include "for.php";

  }


  if ( count($pad_seq_for) ) 
    include "for.php";

  return include "$pad_seq_build.php";

?>