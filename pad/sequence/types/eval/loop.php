<?php
 
  $pSeq_eval_tbl = pExplode ( $pSeq_parm, '|' );

  return pad_var_opts ( $pSeq_loop, $pSeq_eval_tbl );

?>