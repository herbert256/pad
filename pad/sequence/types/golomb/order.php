<?php

  if ( $pad_seq_now == 1 ) return 1;

  return 1 + $pad_seq_base [ $pad_seq_now - ($pad_seq_base [ $pad_seq_base [ $pad_seq_now - 2 ] - 1 ] + 1) ];

?>
