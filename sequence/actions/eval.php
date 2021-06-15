<?php

  $pad_seq_eval_tbl = pad_explode ( $pad_seq_eval, '|' );

  return pad_var_opts ( $pad_sequence, $pad_seq_eval_tbl );

?>