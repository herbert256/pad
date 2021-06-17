<?php

  if ( $pad_sequence == 1 ) return 1;

  return 1 + $pad_seq_base [ $pad_sequence - ($pad_seq_base [ $pad_seq_base [ $pad_sequence - 2 ]-1 ] + 1) ];

?>
