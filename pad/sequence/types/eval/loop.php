<?php
 
  $padSeq_eval_tbl = padExplode ( $padSeq_parm, '|' );

  return padVarOpts ( $padSeq_loop, $padSeq_eval_tbl );

?>