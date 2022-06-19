<?php
 
  $pad_seq_eval_tbl = pad_explode ( $pad_seq_parm, '|' );

  return pad_var_opts ( $pad_seq_now, $pad_seq_eval_tbl );

?>