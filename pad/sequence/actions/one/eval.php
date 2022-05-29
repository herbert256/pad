<?php

  $pad_seq_eval_tbl = pad_explode ( $pad_parms_tag['eval'], '|' );

  return pad_var_opts ( $pad_seq_action, $pad_seq_eval_tbl );

?>