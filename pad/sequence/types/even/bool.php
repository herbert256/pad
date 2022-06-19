<?php

  function pad_seq_now_bool_even( $n ) {

    if ( $n & 1 )
      return FALSE;
    else
      return TRUE;

  }

?>
