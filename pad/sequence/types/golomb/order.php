<?php

  if ( $pad_seq_loop == 1 ) return 1;

  return 1 + $pad_seq_base [ $pad_seq_loop - ($pad_seq_base [ $pad_seq_base [ $pad_seq_loop - 2 ] - 1 ] + 1) ];

?>
