<?php

  if     ( $pad_seq_type == 'from' ) $pad_seq_rand = pad_seq_random ( $pad_seq_from, $pad_seq_to   );
  elseif ( $pad_seq_type == 'min'  ) $pad_seq_rand = pad_seq_random ( $pad_seq_min,  $pad_seq_max  );
  else                               $pad_seq_rand = pad_seq_random ( $pad_seq_init, $pad_seq_exit );

?>