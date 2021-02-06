<?php

  foreach ( $pad_parms_org as $pad_k => $pad_v ) {

    $pad_w = pad_explode ( $pad_v, '=', 2);

    if ( count($pad_w) <> 2           ) return pad_syntax_error ("{set} syntax error (1)");
    if ( substr($pad_w[0],0,1) <> '$' ) return pad_syntax_error ("{set} syntax error (2)");

  }

  return TRUE;

?>