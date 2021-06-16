<?php

  if ( $pad_tag == 'pull' ) {
    $pad_pull_start = TRUE;
    $pad_pull_store = ( $pad_parm     ) ? $pad_parm     : 'pad_seq';
    $pad_tag        = ( $pad_seq_into ) ? $pad_seq_into : 'loop';
    $pad_parm       = '';
  }
  else
    $pad_pull_start = FALSE;

?>