<?php

  $pad_parm_field = $pad_tag;
  $pad_parm_value = '';
  $pad_parm_parms = $pad_parms_val;

  $pad_parm_return = include PAD_HOME . "eval/go/parm.php"; 

  if ( $pad_parm_return === TRUE and $pad_content == '' and ! isset ( $pad_parm_tag ['content'] ) )
    $pad_content = '1'; 

  return $pad_parm_return;

?>