<?php

  foreach ($pad_seq_result as $pad_k => $pad_v) {

    if ( $pad_v % 2 )
      unset ( $pad_seq_result [$pad_k] );

  }

?>