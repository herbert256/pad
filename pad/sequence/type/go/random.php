<?php

  $pad_seq_fromto_rand   = pad_seq_random ( $pad_seq_from, $pad_seq_to   );
  $pad_seq_minmax_rand   = pad_seq_random ( $pad_seq_min,  $pad_seq_max  );
  $pad_seq_initexit_rand = pad_seq_random ( $pad_seq_init, $pad_seq_exit );

  if     ( $pad_seq_type == 'from' ) $pad_seq_rand = $pad_seq_fromto_rand;
  elseif ( $pad_seq_type == 'min'  ) $pad_seq_rand = $pad_seq_minmax_rand;
  else                               $pad_seq_rand = $pad_seq_initexit_rand;

?>